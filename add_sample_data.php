<?php
// Add sample data to the database
$host = 'ytc353.encs.concordia.ca';
$dbname = 'ytc353_1';
$username = 'ytc353_1';
$password = 'Adm1n001';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Adding Sample Data</h2>";
    
    // Add sample location
    $stmt = $pdo->prepare("INSERT INTO Location (locationID, name, type, address, city, province, postalCode, phone, webAddress, maxCapacity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 'Main Gym', 'Head', '123 Main St', 'Montreal', 'QC', 'H3A 1A1', '(514) 555-1234', 'http://example.com', 100]);
    echo "✓ Added sample location<br>";
    
    // Add sample person
    $stmt = $pdo->prepare("INSERT INTO Person (pID, firstName, lastName, dob, ssn, medicare, address, postalCode, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 'John', 'Doe', '1980-01-01', '123-45-6789', 'MED123456', '456 Oak St', 'H3A 1A2', '(514) 555-5678', 'john.doe@email.com']);
    echo "✓ Added sample person<br>";
    
    // Add sample personnel
    $stmt = $pdo->prepare("INSERT INTO Personnel (employeeID, role, mandate) VALUES (?, ?, ?)");
    $stmt->execute([1, 'Coach', 'Salaried']);
    echo "✓ Added sample personnel<br>";
    
    // Add sample family member
    $stmt = $pdo->prepare("INSERT INTO Person (pID, firstName, lastName, dob, ssn, medicare, address, postalCode, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([2, 'Jane', 'Smith', '1985-02-15', '987-65-4321', 'MED654321', '789 Pine St', 'H3A 1A3', '(514) 555-9876', 'jane.smith@email.com']);
    echo "✓ Added sample person for family member<br>";
    
    $stmt = $pdo->prepare("INSERT INTO FamilyMember (familyMemID, relationshipType) VALUES (?, ?)");
    $stmt->execute([2, 'Mother']);
    echo "✓ Added sample family member<br>";
    
    // Add sample club member
    $stmt = $pdo->prepare("INSERT INTO Person (pID, firstName, lastName, dob, ssn, medicare, address, postalCode, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([3, 'Mike', 'Johnson', '2000-05-20', '111-22-3333', 'MED111222', '321 Elm St', 'H3A 1A4', '(514) 555-1111', 'mike.johnson@email.com']);
    echo "✓ Added sample person for club member<br>";
    
    $stmt = $pdo->prepare("INSERT INTO ClubMember (memberID, locationID, memberType, status, height, weight) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([3, 1, 'Major', 'Active', 180.5, 75.2]);
    echo "✓ Added sample club member<br>";
    
    // Add sample team
    $stmt = $pdo->prepare("INSERT INTO Team (teamID, teamName, teamType, locationID) VALUES (?, ?, ?, ?)");
    $stmt->execute([1, 'Eagles', 'male', 1]);
    echo "✓ Added sample team<br>";
    
    // Add sample session
    $stmt = $pdo->prepare("INSERT INTO Session (sessionID, type, date, time, locationID, team1ID, coachID) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 'training', '2024-01-15', '14:00:00', 1, 1, 1]);
    echo "✓ Added sample session<br>";
    
    echo "<br><strong>Sample data added successfully!</strong><br>";
    echo "Now you can test the edit buttons with these sample records.";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 