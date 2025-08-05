<?php
// Database configuration
$host = 'ytc353.encs.concordia.ca';
$dbname = 'ytc353_1';
$username = 'ytc353_1';
$password = 'Adm1n001';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!\n\n";
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Test getSessions function
echo "=== TESTING GETSESSIONS FUNCTION ===\n";
try {
    $stmt = $pdo->query("SELECT s.*, t1.teamName as team1_name, t2.teamName as team2_name, 
                         CONCAT(per.firstName, ' ', per.lastName) as coach_name,
                         CONCAT(s.team1Score, '-', s.team2Score) as score
                         FROM Session s 
                         LEFT JOIN Team t1 ON s.team1ID = t1.teamID 
                         LEFT JOIN Team t2 ON s.team2ID = t2.teamID 
                         LEFT JOIN Personnel p ON t1.headCoachID = p.employeeID 
                         LEFT JOIN Person per ON p.employeeID = per.pID 
                         ORDER BY s.sessionDate DESC, s.startTime DESC LIMIT 5");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($sessions)) {
        echo "No sessions found.\n";
    } else {
        foreach ($sessions as $session) {
            echo "- ID: {$session['sessionID']}, Type: {$session['sessionType']}, Date: {$session['sessionDate']}, Time: {$session['startTime']}\n";
            echo "  Team1: {$session['team1_name']}, Team2: {$session['team2_name']}, Coach: {$session['coach_name']}, Score: {$session['score']}\n";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test getTeams function
echo "=== TESTING GETTEAMS FUNCTION ===\n";
try {
    $stmt = $pdo->query("SELECT t.*, 
                         CONCAT(per.firstName, ' ', per.lastName) as coach_name,
                         l.name as location_name
                         FROM Team t 
                         LEFT JOIN Personnel p ON t.headCoachID = p.employeeID 
                         LEFT JOIN Person per ON p.employeeID = per.pID 
                         LEFT JOIN Location l ON t.locationID = l.locationID 
                         ORDER BY t.teamName LIMIT 5");
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($teams)) {
        echo "No teams found.\n";
    } else {
        foreach ($teams as $team) {
            echo "- ID: {$team['teamID']}, Name: {$team['teamName']}, Gender: {$team['gender']}\n";
            echo "  Coach: {$team['coach_name']}, Location: {$team['location_name']}\n";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test getCoaches function
echo "=== TESTING GETCOACHES FUNCTION ===\n";
try {
    $stmt = $pdo->query("SELECT p.*, per.firstName, per.lastName 
                        FROM Personnel p 
                        LEFT JOIN Person per ON p.employeeID = per.pID 
                        WHERE p.role IN ('Coach', 'AssistantCoach') 
                        ORDER BY per.lastName, per.firstName");
    $coaches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($coaches)) {
        echo "No coaches found.\n";
    } else {
        foreach ($coaches as $coach) {
            echo "- ID: {$coach['employeeID']}, Name: {$coach['firstName']} {$coach['lastName']}, Role: {$coach['role']}\n";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";
?> 