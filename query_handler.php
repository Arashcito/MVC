<?php
// query_handler.php
session_start();

// Database configuration (same as your main file)
$host = 'localhost'; // or '127.0.0.1'
$dbname = 'volleyball_club';
$username = 'root';
$password = 'Radio@33';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
}

// Set content type to JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'execute_question_10':
        executeQuestion10($pdo);
        break;
    case 'execute_question_11':
        executeQuestion11($pdo);
        break;
    case 'execute_question_12':
        executeQuestion12($pdo);
        break;
    case 'execute_question_13':
        executeQuestion13($pdo);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $action]);
        break;
}

function executeQuestion10($pdo) {
    try {
        $locationId = $_POST['location_id'] ?? '';
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        
        // Validate inputs
        if (empty($locationId) || empty($startDate) || empty($endDate)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            return;
        }
        
        // Validate date format
        if (!DateTime::createFromFormat('Y-m-d', $startDate) || !DateTime::createFromFormat('Y-m-d', $endDate)) {
            echo json_encode(['success' => false, 'message' => 'Invalid date format']);
            return;
        }
        
        // Get location name for summary
        $locationStmt = $pdo->prepare("SELECT name FROM Location WHERE locationID = ?");
        $locationStmt->execute([$locationId]);
        $locationInfo = $locationStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$locationInfo) {
            echo json_encode(['success' => false, 'message' => 'Location not found']);
            return;
        }
        
        // Your SQL query
        $sql = "SELECT 
                    T.teamName AS teamName,
                    coach.firstName AS coachFirstName,
                    coach.lastName AS coachLastName,
                    L.address AS address,
                    S.sessionDate,
                    S.startTime,
                    S.sessionType,
                    CASE 
                        WHEN T.teamID = S.team1ID THEN S.team1Score
                        WHEN T.teamID = S.team2ID THEN S.team2Score
                        ELSE NULL
                    END AS score,
                    player.firstName AS playerFirstName,
                    player.lastName AS playerLastName,
                    TM.roleInTeam
                FROM Session AS S
                JOIN Team AS T ON T.teamID = S.team1ID OR T.teamID = S.team2ID
                JOIN Location AS L ON S.locationID = L.locationID
                LEFT JOIN Personnel AS P ON T.headCoachID = P.employeeID
                LEFT JOIN Person AS coach ON P.employeeID = coach.pID
                LEFT JOIN TeamMember AS TM ON TM.teamID = T.teamID
                LEFT JOIN ClubMember AS CM ON TM.memberID = CM.memberID
                LEFT JOIN Person AS player ON CM.memberID = player.pID
                WHERE 
                    S.locationID = ?
                    AND S.sessionDate BETWEEN ? AND ?
                ORDER BY S.sessionDate ASC, S.startTime ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$locationId, $startDate, $endDate]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Create summary message
        $summary = sprintf(
            "Found %d team formation records for %s from %s to %s",
            count($results),
            $locationInfo['name'],
            $startDate,
            $endDate
        );
        
        echo json_encode([
            'success' => true,
            'results' => $results,
            'summary' => $summary,
            'location' => $locationInfo['name'],
            'date_range' => "$startDate to $endDate"
        ]);
        
    } catch (PDOException $e) {
        error_log("Question 10 Query Error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        error_log("Question 10 General Error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

function executeQuestion11($pdo) {
    try {
        // SQL query for Question 11
        $sql = "SELECT 
                    CM.memberID AS memberNumber,
                    P.firstName,
                    P.lastName
                FROM ClubMember AS CM
                JOIN Person AS P ON CM.memberID = P.pID
                JOIN MemberLocationHistory AS MLH ON CM.memberID = MLH.memberID
                WHERE CM.status = 'Inactive'
                  AND TIMESTAMPDIFF(YEAR, (
                        SELECT MIN(startDate)
                        FROM MemberLocationHistory
                        WHERE memberID = CM.memberID
                    ), CURDATE()) >= 2
                GROUP BY CM.memberID, P.firstName, P.lastName
                HAVING COUNT(DISTINCT MLH.locationID) >= 2
                ORDER BY CM.memberID ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Create summary message
        $summary = sprintf(
            "Found %d inactive club members who have been associated with at least 2 locations and are members for at least 2 years",
            count($results)
        );
        
        echo json_encode([
            'success' => true,
            'results' => $results,
            'summary' => $summary
        ]);
        
    } catch (PDOException $e) {
        error_log("Question 11 Query Error: " . $e->getMessage());
        
        // Check if the error is related to missing table
        if (strpos($e->getMessage(), "Table 'mvc_db.MemberLocationHistory' doesn't exist") !== false) {
            echo json_encode([
                'success' => false, 
                'message' => 'The MemberLocationHistory table does not exist in the database. This table is required to track member location associations over time.',
                'suggestion' => 'Please create the MemberLocationHistory table or use an alternative approach to track member location history.'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    } catch (Exception $e) {
        error_log("Question 11 General Error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

function executeQuestion13($pdo) {
    try {
        // SQL query for Question 13
        $sql = "SELECT 
                    CM.memberID AS membershipNumber,
                    P.firstName,
                    P.lastName,
                    TIMESTAMPDIFF(YEAR, P.dob, CURDATE()) AS age,
                    P.phone,
                    P.email,
                    L.name AS locationName
                FROM ClubMember AS CM
                JOIN Person AS P ON CM.memberID = P.pID
                JOIN Location AS L ON CM.locationID = L.locationID
                WHERE CM.status = 'Active'
                  AND CM.memberID NOT IN (
                      SELECT DISTINCT memberID FROM TeamMember
                  )
                ORDER BY locationName ASC, age ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate summary statistics
        $totalMembers = count($results);
        $locationCount = [];
        $ageGroups = ['Under 18' => 0, '18-25' => 0, '26-35' => 0, '36-50' => 0, 'Over 50' => 0];
        
        foreach ($results as $row) {
            // Count by location
            $location = $row['locationName'];
            if (!isset($locationCount[$location])) {
                $locationCount[$location] = 0;
            }
            $locationCount[$location]++;
            
            // Count by age groups
            $age = $row['age'];
            if ($age < 18) {
                $ageGroups['Under 18']++;
            } elseif ($age <= 25) {
                $ageGroups['18-25']++;
            } elseif ($age <= 35) {
                $ageGroups['26-35']++;
            } elseif ($age <= 50) {
                $ageGroups['36-50']++;
            } else {
                $ageGroups['Over 50']++;
            }
        }
        
        // Create summary message
        $locationNames = array_keys($locationCount);
        $locationSummary = count($locationNames) > 0 ? 
            'across ' . count($locationNames) . ' locations (' . implode(', ', array_slice($locationNames, 0, 3)) . 
            (count($locationNames) > 3 ? ', and ' . (count($locationNames) - 3) . ' more)' : ')') : '';
        
        $summary = sprintf(
            "Found %d active club members who have never been assigned to any team formation %s",
            $totalMembers,
            $locationSummary
        );
        
        echo json_encode([
            'success' => true,
            'results' => $results,
            'summary' => $summary,
            'stats' => [
                'totalMembers' => $totalMembers,
                'locationCount' => $locationCount,
                'ageGroups' => $ageGroups,
                'locationsAffected' => count($locationNames)
            ]
        ]);
        
    } catch (PDOException $e) {
        error_log("Question 13 Query Error: " . $e->getMessage());
        
        // Check for common table issues
        if (strpos($e->getMessage(), "Table") !== false && strpos($e->getMessage(), "doesn't exist") !== false) {
            echo json_encode([
                'success' => false, 
                'message' => 'Required database table not found: ' . $e->getMessage(),
                'suggestion' => 'Please ensure all required tables (ClubMember, Person, Location, TeamMember) exist in the database.'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    } catch (Exception $e) {
        error_log("Question 13 General Error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

function executeQuestion12($pdo) {
    try {
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        
        // Validate inputs
        if (empty($startDate) || empty($endDate)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters: start date and end date']);
            return;
        }
        
        // Validate date format
        if (!DateTime::createFromFormat('Y-m-d', $startDate) || !DateTime::createFromFormat('Y-m-d', $endDate)) {
            echo json_encode(['success' => false, 'message' => 'Invalid date format. Please use YYYY-MM-DD format.']);
            return;
        }
        
        // Validate date range
        if (strtotime($startDate) > strtotime($endDate)) {
            echo json_encode(['success' => false, 'message' => 'Start date must be before or equal to end date.']);
            return;
        }
        
        // SQL query for Question 12
        $sql = "SELECT 
                    L.name AS locationName,
                    SUM(CASE WHEN S.sessionType = 'Training' THEN 1 ELSE 0 END) AS totalTrainingSessions,
                    SUM(CASE WHEN S.sessionType = 'Training' THEN TMCount.numPlayers ELSE 0 END) AS totalTrainingPlayers, 
                    SUM(CASE WHEN S.sessionType = 'Game' THEN 1 ELSE 0 END) AS totalGameSessions,
                    SUM(CASE WHEN S.sessionType = 'Game' THEN TMCount.numPlayers ELSE 0 END) AS totalGamePlayers
                FROM Location AS L
                JOIN Team AS T ON L.locationID = T.locationID
                JOIN Session AS S ON T.teamID = S.team1ID OR T.teamID = S.team2ID
                LEFT JOIN (
                    SELECT teamID, COUNT(*) AS numPlayers
                    FROM TeamMember
                    GROUP BY teamID
                ) AS TMCount ON T.teamID = TMCount.teamID
                WHERE S.sessionDate BETWEEN ? AND ?
                GROUP BY L.locationID, L.name
                HAVING SUM(CASE WHEN S.sessionType = 'Game' THEN 1 ELSE 0 END) >= 4
                ORDER BY totalGameSessions DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate summary statistics
        $totalLocations = count($results);
        $totalGameSessions = 0;
        $totalTrainingSessions = 0;
        $totalGamePlayers = 0;
        $totalTrainingPlayers = 0;
        
        foreach ($results as $row) {
            $totalGameSessions += $row['totalGameSessions'];
            $totalTrainingSessions += $row['totalTrainingSessions'];
            $totalGamePlayers += $row['totalGamePlayers'];
            $totalTrainingPlayers += $row['totalTrainingPlayers'];
        }
        
        // Create summary message
        $summary = sprintf(
            "Found %d locations with at least 4 game sessions from %s to %s. Total: %d game sessions (%d players), %d training sessions (%d players)",
            $totalLocations,
            $startDate,
            $endDate,
            $totalGameSessions,
            $totalGamePlayers,
            $totalTrainingSessions,
            $totalTrainingPlayers
        );
        
        echo json_encode([
            'success' => true,
            'results' => $results,
            'summary' => $summary,
            'period' => "$startDate to $endDate",
            'stats' => [
                'totalLocations' => $totalLocations,
                'totalGameSessions' => $totalGameSessions,
                'totalTrainingSessions' => $totalTrainingSessions,
                'totalGamePlayers' => $totalGamePlayers,
                'totalTrainingPlayers' => $totalTrainingPlayers
            ]
        ]);
        
    } catch (PDOException $e) {
        error_log("Question 12 Query Error: " . $e->getMessage());
        
        // Check for common table issues
        if (strpos($e->getMessage(), "Table") !== false && strpos($e->getMessage(), "doesn't exist") !== false) {
            echo json_encode([
                'success' => false, 
                'message' => 'Required database table not found: ' . $e->getMessage(),
                'suggestion' => 'Please ensure all required tables (Location, Team, Session, TeamMember) exist in the database.'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    } catch (Exception $e) {
        error_log("Question 12 General Error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
?>