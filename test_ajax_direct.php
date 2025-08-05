<?php
// Test AJAX handler directly
echo "<h2>Testing AJAX Handler Directly</h2>";

// Simulate POST data
$_POST['action'] = 'get_personnel';
$_POST['id'] = '1';

echo "<p>Testing action: " . $_POST['action'] . " with id: " . $_POST['id'] . "</p>";

// Include the AJAX handler
ob_start();
include 'ajax_handler.php';
$output = ob_get_clean();

echo "<h3>Output:</h3>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Also test database connection directly
echo "<h3>Testing Database Connection:</h3>";
$host = 'ytc353.encs.concordia.ca';
$dbname = 'ytc353_1';
$username = 'ytc353_1';
$password = 'Adm1n001';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Test the actual query
    $stmt = $pdo->prepare("SELECT p.*, per.* FROM Personnel p 
                          LEFT JOIN Person per ON p.employeeID = per.pID 
                          WHERE p.employeeID = ?");
    $stmt->execute([1]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "<p style='color: green;'>✓ Query successful, found data:</p>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    } else {
        echo "<p style='color: orange;'>⚠ Query successful but no data found for ID 1</p>";
        
        // Check what IDs exist
        $stmt = $pdo->query("SELECT employeeID FROM Personnel LIMIT 5");
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Available Personnel IDs: " . implode(', ', $ids) . "</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}
?> 