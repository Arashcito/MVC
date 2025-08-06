<?php
// Location Info Handler - Complete location details with statistics

function getLocationInfo($pdo) {
    try {
        // Step 1: Get all location IDs ordered by province and city
        $locationQuery = "
            SELECT locationID
            FROM Location loc
            JOIN PostalAreaInfo pai ON loc.postalCode = pai.postalCode
            ORDER BY pai.province ASC, pai.city ASC
        ";
        
        $locationStmt = $pdo->prepare($locationQuery);
        $locationStmt->execute();
        $locationIDs = $locationStmt->fetchAll(PDO::FETCH_COLUMN);
        
        $locationDetails = [];
        
        foreach ($locationIDs as $locationID) {
            // Step 2: Get detailed location information
            $detailQuery = "
                SELECT 
                    loc.locationID,
                    loc.address, 
                    pai.city, 
                    pai.province, 
                    loc.postalCode, 
                    GROUP_CONCAT(lp.phone SEPARATOR ', ') as phones,
                    loc.webAddress, 
                    loc.type, 
                    loc.maxCapacity, 
                    CONCAT(pn.firstName, ' ', pn.lastName) as managerName
                FROM Location loc
                JOIN PostalAreaInfo pai ON loc.postalCode = pai.postalCode
                LEFT JOIN LocationPhone lp ON loc.locationID = lp.locationID
                LEFT JOIN Personnel per ON loc.managerID = per.employeeID
                LEFT JOIN Person pn ON per.employeeID = pn.pID
                WHERE loc.locationID = ?
                GROUP BY loc.locationID, loc.address, pai.city, pai.province, loc.postalCode, 
                         loc.webAddress, loc.type, loc.maxCapacity, pn.firstName, pn.lastName
            ";
            
            $detailStmt = $pdo->prepare($detailQuery);
            $detailStmt->execute([$locationID]);
            $location = $detailStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($location) {
                // Step 3: Count minor members
                $minorQuery = "
                    SELECT COUNT(cm.memberID) as minorCount
                    FROM ClubMember cm
                    WHERE cm.memberType = 'Minor' AND cm.locationID = ?
                ";
                $minorStmt = $pdo->prepare($minorQuery);
                $minorStmt->execute([$locationID]);
                $minorCount = $minorStmt->fetchColumn();
                
                // Step 4: Count major members
                $majorQuery = "
                    SELECT COUNT(cm.memberID) as majorCount
                    FROM ClubMember cm
                    WHERE cm.memberType = 'Major' AND cm.locationID = ?
                ";
                $majorStmt = $pdo->prepare($majorQuery);
                $majorStmt->execute([$locationID]);
                $majorCount = $majorStmt->fetchColumn();
                
                // Step 5: Count teams
                $teamQuery = "
                    SELECT COUNT(t.teamID) as teamCount
                    FROM Team t
                    WHERE t.locationID = ?
                ";
                $teamStmt = $pdo->prepare($teamQuery);
                $teamStmt->execute([$locationID]);
                $teamCount = $teamStmt->fetchColumn();
                
                // Combine all data
                $location['minorMemberCount'] = $minorCount ?: 0;
                $location['majorMemberCount'] = $majorCount ?: 0;
                $location['teamCount'] = $teamCount ?: 0;
                
                // Clean up null values
                $location['phones'] = $location['phones'] ?: 'No phone numbers';
                $location['webAddress'] = $location['webAddress'] ?: 'No website';
                $location['managerName'] = $location['managerName'] ?: 'No manager assigned';
                
                $locationDetails[] = $location;
            }
        }
        
        return [
            'success' => true,
            'data' => $locationDetails,
            'count' => count($locationDetails)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error retrieving location information: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

// Function to export location data to CSV
function exportLocationInfoCSV($pdo) {
    $result = getLocationInfo($pdo);
    
    if (!$result['success']) {
        return $result;
    }
    
    $filename = 'location_info_' . date('Y-m-d_H-i-s') . '.csv';
    $filepath = __DIR__ . '/exports/' . $filename;
    
    // Create exports directory if it doesn't exist
    if (!is_dir(__DIR__ . '/exports')) {
        mkdir(__DIR__ . '/exports', 0755, true);
    }
    
    $file = fopen($filepath, 'w');
    
    // CSV Headers
    $headers = [
        'Location ID', 'Address', 'City', 'Province', 'Postal Code',
        'Phone Numbers', 'Website', 'Type', 'Capacity', 'Manager Name',
        'Minor Members', 'Major Members', 'Teams'
    ];
    
    fputcsv($file, $headers);
    
    // CSV Data
    foreach ($result['data'] as $location) {
        fputcsv($file, [
            $location['locationID'],
            $location['address'],
            $location['city'],
            $location['province'],
            $location['postalCode'],
            $location['phones'],
            $location['webAddress'],
            $location['type'],
            $location['maxCapacity'],
            $location['managerName'],
            $location['minorMemberCount'],
            $location['majorMemberCount'],
            $location['teamCount']
        ]);
    }
    
    fclose($file);
    
    return [
        'success' => true,
        'message' => 'Location info exported successfully',
        'filename' => $filename,
        'filepath' => $filepath
    ];
}
?>
