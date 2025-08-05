<?php
// Test database connection and table structure
$host = 'ytc353.encs.concordia.ca';
$dbname = 'ytc353_1';
$username = 'ytc353_1';
$password = 'Adm1n001';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!<br>";
    
    // Test Personnel query
    $stmt = $pdo->query("SELECT p.*, per.firstName, per.lastName, per.dob, per.ssn, per.medicare, 
                               per.address, per.postalCode, per.phone, per.email, l.name as location_name 
                        FROM Personnel p 
                        LEFT JOIN Person per ON p.employeeID = per.pID 
                        LEFT JOIN Location l ON p.employeeID = l.managerID 
                        ORDER BY per.lastName");
    $personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Personnel count: " . count($personnel) . "<br>";
    
    // Test FamilyMember query
    $stmt = $pdo->query("SELECT fm.*, per.firstName, per.lastName, per.dob, per.ssn, per.medicare, 
                               per.address, per.postalCode, per.phone, per.email, l.name as location_name 
                        FROM FamilyMember fm 
                        LEFT JOIN Person per ON fm.familyMemID = per.pID 
                        LEFT JOIN Location l ON fm.familyMemID = l.locationID 
                        ORDER BY per.lastName");
    $family = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Family members count: " . count($family) . "<br>";
    
    // Test ClubMember query
    $stmt = $pdo->query("SELECT cm.*, per.firstName, per.lastName, per.dob, per.ssn, per.medicare, 
                               per.address, per.postalCode, per.phone, per.email, l.name as location_name 
                        FROM ClubMember cm 
                        LEFT JOIN Person per ON cm.memberID = per.pID 
                        LEFT JOIN Location l ON cm.locationID = l.locationID 
                        ORDER BY per.lastName");
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Club members count: " . count($members) . "<br>";
    
    // Test Session query
    $stmt = $pdo->query("SELECT s.*, t1.teamName as team1_name, t2.teamName as team2_name, 
                               l.name as location_name, l.address as location_address,
                               CONCAT(p.firstName, ' ', p.lastName) as coach_name
                        FROM Session s 
                        LEFT JOIN Team t1 ON s.team1ID = t1.teamID 
                        LEFT JOIN Team t2 ON s.team2ID = t2.teamID 
                        LEFT JOIN Location l ON s.locationID = l.locationID 
                        LEFT JOIN Personnel per ON s.coachID = per.employeeID 
                        LEFT JOIN Person p ON per.employeeID = p.pID 
                        ORDER BY s.date DESC, s.time DESC");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Sessions count: " . count($sessions) . "<br>";
    
    echo "All queries successful!";
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?> 