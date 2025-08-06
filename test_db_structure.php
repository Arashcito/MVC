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

// Check what tables exist
echo "=== EXISTING TABLES ===\n";
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    echo "- $table\n";
}
echo "\n";

// Check Sessions table structure
echo "=== SESSIONS TABLE STRUCTURE ===\n";
try {
    $stmt = $pdo->query("DESCRIBE Sessions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']} ({$column['Null']})\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Check Sessions data
echo "=== SESSIONS DATA ===\n";
try {
    $stmt = $pdo->query("SELECT * FROM Sessions LIMIT 5");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($sessions)) {
        echo "No sessions found in database.\n";
    } else {
        foreach ($sessions as $session) {
            echo "- ID: {$session['id']}, Type: {$session['type']}, Date: {$session['date']}, Time: {$session['time']}\n";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Check Teams table structure
echo "=== TEAMS TABLE STRUCTURE ===\n";
try {
    $stmt = $pdo->query("DESCRIBE Teams");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']} ({$column['Null']})\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Check Teams data
echo "=== TEAMS DATA ===\n";
try {
    $stmt = $pdo->query("SELECT * FROM Teams LIMIT 5");
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($teams)) {
        echo "No teams found in database.\n";
    } else {
        foreach ($teams as $team) {
            echo "- ID: {$team['teamID']}, Name: {$team['teamName']}, Gender: {$team['gender']}\n";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Check Personnel table structure
echo "=== PERSONNEL TABLE STRUCTURE ===\n";
try {
    $stmt = $pdo->query("DESCRIBE Personnel");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']} ({$column['Null']})\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Check Personnel data
echo "=== PERSONNEL DATA ===\n";
try {
    $stmt = $pdo->query("SELECT * FROM Personnel WHERE role IN ('Coach', 'Assistant Coach') LIMIT 5");
    $personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($personnel)) {
        echo "No coaches found in database.\n";
    } else {
        foreach ($personnel as $person) {
            echo "- ID: {$person['pID']}, Role: {$person['role']}\n";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test the getSessions function
echo "=== TESTING GETSESSIONS FUNCTION ===\n";
try {
    $stmt = $pdo->query("SELECT s.*, t1.teamName as team1_name, t2.teamName as team2_name, 
                         l.name as location_name, l.address as location_address,
                         CONCAT(per.firstName, ' ', per.lastName) as coach_name
                         FROM Sessions s 
                         LEFT JOIN Teams t1 ON s.team1ID = t1.teamID 
                         LEFT JOIN Teams t2 ON s.team2ID = t2.teamID 
                         LEFT JOIN Location l ON s.locationID = l.locationID 
                         LEFT JOIN Personnel p ON s.coachID = p.pID 
                         LEFT JOIN Person per ON p.pID = per.pID 
                         ORDER BY s.date DESC, s.time DESC");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($sessions)) {
        echo "No sessions found with JOIN query.\n";
    } else {
        foreach ($sessions as $session) {
            echo "- ID: {$session['id']}, Type: {$session['type']}, Date: {$session['date']}, Coach: {$session['coach_name']}\n";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test the getTeams function
echo "=== TESTING GETTEAMS FUNCTION ===\n";
try {
    $stmt = $pdo->query("SELECT t.*, 
                         CONCAT(p.firstName, ' ', p.lastName) as coach_name,
                         l.name as location_name
                         FROM Teams t 
                         LEFT JOIN Personnel p ON t.headCoachID = p.pID 
                         LEFT JOIN Location l ON t.locationID = l.locationID 
                         ORDER BY t.teamName");
    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($teams)) {
        echo "No teams found with JOIN query.\n";
    } else {
        foreach ($teams as $team) {
            echo "- ID: {$team['teamID']}, Name: {$team['teamName']}, Coach: {$team['coach_name']}\n";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";
?> 