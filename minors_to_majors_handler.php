<?php
// Minors to Majors Handler - Get major members who started as minors

function getMinorsToMajorsReport($pdo) {
    try {
        // Get all active major club members who have been members since they were minors
        $reportQuery = "
            SELECT 
                cm.memberID as membershipNumber,
                p.firstName,
                p.lastName,
                cm.dateJoined as dateOfJoining,
                TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as currentAge,
                p.phone,
                p.email,
                l.name as currentLocationName
                
            FROM ClubMember cm
            JOIN Person p ON cm.memberID = p.pID
            JOIN Location l ON cm.locationID = l.locationID

            WHERE cm.status = 'Active'
              AND cm.memberType = 'Major'
              -- Since familyMemID is NOT NULL, they were minors (only minors have family members)
              AND cm.familyMemID IS NOT NULL
              -- Additional check: they were under 18 when they joined
              AND TIMESTAMPDIFF(YEAR, p.dob, cm.dateJoined) < 18

            ORDER BY l.name ASC, TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) ASC
        ";
        
        $stmt = $pdo->prepare($reportQuery);
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate additional statistics
        $totalMembers = count($members);
        $locationStats = [];
        $ageStats = [];
        
        foreach ($members as $member) {
            // Location statistics
            $location = $member['currentLocationName'];
            if (!isset($locationStats[$location])) {
                $locationStats[$location] = 0;
            }
            $locationStats[$location]++;
            
            // Age group statistics
            $age = $member['currentAge'];
            $ageGroup = '';
            if ($age < 25) {
                $ageGroup = '18-24';
            } elseif ($age < 35) {
                $ageGroup = '25-34';
            } elseif ($age < 45) {
                $ageGroup = '35-44';
            } elseif ($age < 55) {
                $ageGroup = '45-54';
            } else {
                $ageGroup = '55+';
            }
            
            if (!isset($ageStats[$ageGroup])) {
                $ageStats[$ageGroup] = 0;
            }
            $ageStats[$ageGroup]++;
        }
        
        return [
            'success' => true,
            'data' => $members,
            'totalMembers' => $totalMembers,
            'locationStats' => $locationStats,
            'ageStats' => $ageStats
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error retrieving minors to majors report: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

function getMinorsToMajorsByLocation($pdo, $locationName = null) {
    try {
        $whereClause = "
            WHERE cm.status = 'Active'
              AND cm.memberType = 'Major'
              AND cm.familyMemID IS NOT NULL
              AND TIMESTAMPDIFF(YEAR, p.dob, cm.dateJoined) < 18
        ";
        
        $params = [];
        
        if ($locationName && $locationName !== 'all') {
            $whereClause .= " AND l.name = ?";
            $params[] = $locationName;
        }
        
        $reportQuery = "
            SELECT 
                cm.memberID as membershipNumber,
                p.firstName,
                p.lastName,
                cm.dateJoined as dateOfJoining,
                TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as currentAge,
                TIMESTAMPDIFF(YEAR, p.dob, cm.dateJoined) as ageWhenJoined,
                p.phone,
                p.email,
                l.name as currentLocationName,
                TIMESTAMPDIFF(YEAR, cm.dateJoined, CURDATE()) as yearsAsMember
                
            FROM ClubMember cm
            JOIN Person p ON cm.memberID = p.pID
            JOIN Location l ON cm.locationID = l.locationID
            
            $whereClause

            ORDER BY l.name ASC, TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) ASC
        ";
        
        $stmt = $pdo->prepare($reportQuery);
        $stmt->execute($params);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $members,
            'count' => count($members)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error retrieving filtered report: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

function getAvailableLocations($pdo) {
    try {
        $locationQuery = "
            SELECT DISTINCT l.name as locationName
            FROM Location l
            JOIN ClubMember cm ON l.locationID = cm.locationID
            WHERE cm.status = 'Active'
              AND cm.memberType = 'Major'
              AND cm.familyMemID IS NOT NULL
              AND EXISTS (
                  SELECT 1 FROM Person p 
                  WHERE p.pID = cm.memberID 
                  AND TIMESTAMPDIFF(YEAR, p.dob, cm.dateJoined) < 18
              )
            ORDER BY l.name ASC
        ";
        
        $stmt = $pdo->prepare($locationQuery);
        $stmt->execute();
        $locations = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return [
            'success' => true,
            'data' => $locations
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error retrieving locations: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

// Function to export minors to majors data to CSV
function exportMinorsToMajorsCSV($pdo, $locationFilter = null) {
    if ($locationFilter && $locationFilter !== 'all') {
        $result = getMinorsToMajorsByLocation($pdo, $locationFilter);
    } else {
        $result = getMinorsToMajorsReport($pdo);
    }
    
    if (!$result['success']) {
        return $result;
    }
    
    $filename = 'minors_to_majors_' . date('Y-m-d_H-i-s') . '.csv';
    $filepath = __DIR__ . '/exports/' . $filename;
    
    // Create exports directory if it doesn't exist
    if (!is_dir(__DIR__ . '/exports')) {
        mkdir(__DIR__ . '/exports', 0755, true);
    }
    
    $file = fopen($filepath, 'w');
    
    // CSV Headers
    $headers = [
        'Membership Number', 'First Name', 'Last Name', 'Date of Joining',
        'Current Age', 'Age When Joined', 'Phone', 'Email', 'Current Location',
        'Years as Member'
    ];
    
    fputcsv($file, $headers);
    
    // CSV Data
    foreach ($result['data'] as $member) {
        $ageWhenJoined = isset($member['ageWhenJoined']) ? $member['ageWhenJoined'] : 'N/A';
        $yearsAsMember = isset($member['yearsAsMember']) ? $member['yearsAsMember'] : 'N/A';
        
        fputcsv($file, [
            $member['membershipNumber'],
            $member['firstName'],
            $member['lastName'],
            $member['dateOfJoining'],
            $member['currentAge'],
            $ageWhenJoined,
            $member['phone'] ?: 'No phone',
            $member['email'] ?: 'No email',
            $member['currentLocationName'],
            $yearsAsMember
        ]);
    }
    
    fclose($file);
    
    return [
        'success' => true,
        'message' => 'Minors to majors report exported successfully',
        'filename' => $filename,
        'filepath' => $filepath
    ];
}
?>
