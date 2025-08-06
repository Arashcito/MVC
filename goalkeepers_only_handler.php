<?php
// Goalkeepers Only Handler - Get members who have only been assigned as goalkeepers

function getGoalkeepersOnlyReport($pdo) {
    try {
        // Get all active club members who have only been assigned as goalkeepers
        $reportQuery = "
            SELECT 
                cm.memberID as membershipNumber,
                p.firstName,
                p.lastName,
                TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as age,
                p.phone,
                p.email,
                l.name as currentLocationName
            FROM ClubMember cm
            JOIN Person p ON cm.memberID = p.pID
            JOIN Location l ON cm.locationID = l.locationID
            WHERE cm.status = 'Active'
              AND cm.memberID IN (
                  SELECT DISTINCT sp.participantID
                  FROM SessionParticipation sp
                  WHERE sp.roleInSession = 'Goalkeeper'
              )
              AND cm.memberID NOT IN (
                  SELECT DISTINCT sp.participantID
                  FROM SessionParticipation sp
                  WHERE sp.roleInSession IN ('Defender', 'Midfielder', 'Forward', 'HeadCoach')
              )
            ORDER BY l.name ASC, cm.memberID ASC
        ";
        
        $stmt = $pdo->prepare($reportQuery);
        $stmt->execute();
        $goalkeepers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate additional statistics
        $totalGoalkeepers = count($goalkeepers);
        $locationStats = [];
        $ageStats = [];
        
        foreach ($goalkeepers as $goalkeeper) {
            // Location statistics
            $location = $goalkeeper['currentLocationName'];
            if (!isset($locationStats[$location])) {
                $locationStats[$location] = 0;
            }
            $locationStats[$location]++;
            
            // Age group statistics
            $age = $goalkeeper['age'];
            $ageGroup = '';
            if ($age < 18) {
                $ageGroup = 'Under 18';
            } elseif ($age < 25) {
                $ageGroup = '18-24';
            } elseif ($age < 35) {
                $ageGroup = '25-34';
            } elseif ($age < 45) {
                $ageGroup = '35-44';
            } else {
                $ageGroup = '45+';
            }
            
            if (!isset($ageStats[$ageGroup])) {
                $ageStats[$ageGroup] = 0;
            }
            $ageStats[$ageGroup]++;
        }
        
        return [
            'success' => true,
            'data' => $goalkeepers,
            'totalGoalkeepers' => $totalGoalkeepers,
            'locationStats' => $locationStats,
            'ageStats' => $ageStats
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error retrieving goalkeepers only report: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

function getGoalkeepersByLocation($pdo, $locationName = null) {
    try {
        $whereClause = "
            WHERE cm.status = 'Active'
              AND cm.memberID IN (
                  SELECT DISTINCT sp.participantID
                  FROM SessionParticipation sp
                  WHERE sp.roleInSession = 'Goalkeeper'
              )
              AND cm.memberID NOT IN (
                  SELECT DISTINCT sp.participantID
                  FROM SessionParticipation sp
                  WHERE sp.roleInSession IN ('Defender', 'Midfielder', 'Forward', 'HeadCoach')
              )
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
                TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as age,
                p.phone,
                p.email,
                l.name as currentLocationName,
                cm.memberType,
                cm.dateJoined
            FROM ClubMember cm
            JOIN Person p ON cm.memberID = p.pID
            JOIN Location l ON cm.locationID = l.locationID
            
            $whereClause

            ORDER BY l.name ASC, cm.memberID ASC
        ";
        
        $stmt = $pdo->prepare($reportQuery);
        $stmt->execute($params);
        $goalkeepers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $goalkeepers,
            'count' => count($goalkeepers)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error retrieving filtered goalkeepers report: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

function getGoalkeeperSessionCount($pdo, $memberID) {
    try {
        $sessionQuery = "
            SELECT COUNT(*) as sessionCount
            FROM SessionParticipation sp
            WHERE sp.participantID = ? 
            AND sp.roleInSession = 'Goalkeeper'
        ";
        
        $stmt = $pdo->prepare($sessionQuery);
        $stmt->execute([$memberID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['sessionCount'] ?: 0;
        
    } catch (Exception $e) {
        return 0;
    }
}

function getAvailableGoalkeeperLocations($pdo) {
    try {
        $locationQuery = "
            SELECT DISTINCT l.name as locationName
            FROM Location l
            JOIN ClubMember cm ON l.locationID = cm.locationID
            WHERE cm.status = 'Active'
              AND cm.memberID IN (
                  SELECT DISTINCT sp.participantID
                  FROM SessionParticipation sp
                  WHERE sp.roleInSession = 'Goalkeeper'
              )
              AND cm.memberID NOT IN (
                  SELECT DISTINCT sp.participantID
                  FROM SessionParticipation sp
                  WHERE sp.roleInSession IN ('Defender', 'Midfielder', 'Forward', 'HeadCoach')
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

// Function to export goalkeepers only data to CSV
function exportGoalkeepersOnlyCSV($pdo, $locationFilter = null) {
    if ($locationFilter && $locationFilter !== 'all') {
        $result = getGoalkeepersByLocation($pdo, $locationFilter);
    } else {
        $result = getGoalkeepersOnlyReport($pdo);
    }
    
    if (!$result['success']) {
        return $result;
    }
    
    $filename = 'goalkeepers_only_' . date('Y-m-d_H-i-s') . '.csv';
    $filepath = __DIR__ . '/exports/' . $filename;
    
    // Create exports directory if it doesn't exist
    if (!is_dir(__DIR__ . '/exports')) {
        mkdir(__DIR__ . '/exports', 0755, true);
    }
    
    $file = fopen($filepath, 'w');
    
    // CSV Headers
    $headers = [
        'Membership Number', 'First Name', 'Last Name', 'Age',
        'Phone', 'Email', 'Current Location', 'Member Type', 'Date Joined',
        'Goalkeeper Sessions'
    ];
    
    fputcsv($file, $headers);
    
    // CSV Data
    foreach ($result['data'] as $goalkeeper) {
        $memberType = isset($goalkeeper['memberType']) ? $goalkeeper['memberType'] : 'N/A';
        $dateJoined = isset($goalkeeper['dateJoined']) ? $goalkeeper['dateJoined'] : 'N/A';
        $sessionCount = getGoalkeeperSessionCount($pdo, $goalkeeper['membershipNumber']);
        
        fputcsv($file, [
            $goalkeeper['membershipNumber'],
            $goalkeeper['firstName'],
            $goalkeeper['lastName'],
            $goalkeeper['age'],
            $goalkeeper['phone'] ?: 'No phone',
            $goalkeeper['email'] ?: 'No email',
            $goalkeeper['currentLocationName'],
            $memberType,
            $dateJoined,
            $sessionCount
        ]);
    }
    
    fclose($file);
    
    return [
        'success' => true,
        'message' => 'Goalkeepers only report exported successfully',
        'filename' => $filename,
        'filepath' => $filepath
    ];
}
?>
