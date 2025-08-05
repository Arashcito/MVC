<?php
// Add test data for sessions and teams
$host = 'ytc353.encs.concordia.ca';
$dbname = 'ytc353_1';
$username = 'ytc353_1';
$password = 'Adm1n001';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Adding Test Data for Sessions and Teams</h2>";
    
    // First, let's check what tables exist
    echo "<h3>Checking existing tables:</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "- $table<br>";
    }
    echo "<br>";
    
    // Check if we have any existing data
    echo "<h3>Checking existing data:</h3>";
    
    // Check Sessions
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Session");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Sessions: " . $result['count'] . "<br>";
    } catch (PDOException $e) {
        echo "Sessions table error: " . $e->getMessage() . "<br>";
    }
    
    // Check Teams
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Team");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Teams: " . $result['count'] . "<br>";
    } catch (PDOException $e) {
        echo "Teams table error: " . $e->getMessage() . "<br>";
    }
    
    // Check Personnel (coaches)
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Personnel WHERE role IN ('Coach', 'Assistant Coach')");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Coaches: " . $result['count'] . "<br>";
    } catch (PDOException $e) {
        echo "Personnel table error: " . $e->getMessage() . "<br>";
    }
    
    // Check Locations
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Location");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Locations: " . $result['count'] . "<br>";
    } catch (PDOException $e) {
        echo "Location table error: " . $e->getMessage() . "<br>";
    }
    
    echo "<br>";
    
    // Add a test team if none exist
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Team");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            // Add a test team
            $stmt = $pdo->prepare("INSERT INTO Team (teamID, teamName, teamType, locationID) VALUES (?, ?, ?, ?)");
            $stmt->execute([1, 'Test Eagles', 'male', 1]);
            echo "✓ Added test team<br>";
        } else {
            echo "Teams already exist<br>";
        }
    } catch (PDOException $e) {
        echo "Error adding team: " . $e->getMessage() . "<br>";
    }
    
    // Add a test session if none exist
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Session");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            // Add a test session
            $stmt = $pdo->prepare("INSERT INTO Session (sessionID, type, date, time, locationID, team1ID, coachID) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([1, 'training', '2024-01-15', '14:00:00', 1, 1, 1]);
            echo "✓ Added test session<br>";
        } else {
            echo "Sessions already exist<br>";
        }
    } catch (PDOException $e) {
        echo "Error adding session: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><strong>Test data setup complete!</strong><br>";
    echo "Now check your main application to see if sessions and teams are displaying.";
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?> 