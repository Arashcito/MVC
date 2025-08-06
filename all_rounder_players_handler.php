<?php
// All-Rounder Players Handler - Get members who have played all positions

function getAllRounderPlayersReport($pdo) {
    try {
        // Get all active club members who have played all four positions in game sessions
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
              AND EXISTS (
                  -- At least one game as Goalkeeper
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Goalkeeper'
              )
              AND EXISTS (
                  -- At least one game as Defender
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Defender'
              )
              AND EXISTS (
                  -- At least one game as Midfielder
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Midfielder'
              )
              AND EXISTS (
                  -- At least one game as Forward
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Forward'
              )
            ORDER BY l.name ASC, cm.memberID ASC
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
            $age = $member['age'];
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
            'message' => 'Error retrieving all-rounder players report: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

function getAllRounderPlayersByLocation($pdo, $locationName = null) {
    try {
        $whereClause = "
            WHERE cm.status = 'Active'
              AND EXISTS (
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Goalkeeper'
              )
              AND EXISTS (
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Defender'
              )
              AND EXISTS (
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Midfielder'
              )
              AND EXISTS (
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Forward'
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
                l.name as currentLocationName
                
            FROM ClubMember cm
            JOIN Person p ON cm.memberID = p.pID
            JOIN Location l ON cm.locationID = l.locationID
            
            $whereClause

            ORDER BY l.name ASC, cm.memberID ASC
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

function getAllRounderLocations($pdo) {
    try {
        $locationQuery = "
            SELECT DISTINCT l.name as locationName
            FROM Location l
            JOIN ClubMember cm ON l.locationID = cm.locationID
            WHERE cm.status = 'Active'
              AND EXISTS (
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Goalkeeper'
              )
              AND EXISTS (
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Defender'
              )
              AND EXISTS (
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Midfielder'
              )
              AND EXISTS (
                  SELECT 1 FROM SessionParticipation sp
                  JOIN Session s ON sp.sessionID = s.sessionID
                  WHERE sp.participantID = cm.memberID 
                    AND s.sessionType = 'Game'
                    AND sp.roleInSession = 'Forward'
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

// Function to export all-rounder players data to CSV
function exportAllRounderPlayersCSV($pdo, $locationFilter = null) {
    if ($locationFilter && $locationFilter !== 'all') {
        $result = getAllRounderPlayersByLocation($pdo, $locationFilter);
    } else {
        $result = getAllRounderPlayersReport($pdo);
    }
    
    if (!$result['success']) {
        return $result;
    }
    
    $filename = 'all_rounder_players_' . date('Y-m-d_H-i-s') . '.csv';
    $filepath = __DIR__ . '/exports/' . $filename;
    
    // Create exports directory if it doesn't exist
    if (!is_dir(__DIR__ . '/exports')) {
        mkdir(__DIR__ . '/exports', 0755, true);
    }
    
    $file = fopen($filepath, 'w');
    
    // CSV Headers
    $headers = [
        'Membership Number', 'First Name', 'Last Name', 'Age',
        'Phone', 'Email', 'Current Location'
    ];
    
    fputcsv($file, $headers);
    
    // CSV Data
    foreach ($result['data'] as $member) {
        fputcsv($file, [
            $member['membershipNumber'],
            $member['firstName'],
            $member['lastName'],
            $member['age'],
            $member['phone'] ?: 'No phone',
            $member['email'] ?: 'No email',
            $member['currentLocationName']
        ]);
    }
    
    fclose($file);
    
    return [
        'success' => true,
        'message' => 'All-rounder players report exported successfully',
        'filename' => $filename,
        'filepath' => $filepath
    ];
}
?>
