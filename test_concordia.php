<?php
// Test file for Concordia Server Deployment
echo "<h1>🏐 Montréal Volleyball Club - Concordia Server Test</h1>";
echo "<p><strong>Current date and time:</strong> " . date("D M d, Y H:i:s", time()) . "</p>";
echo "<p><strong>PHP version:</strong> " . phpversion() . "</p>";

// Test database connection
$host = 'ytc353.encs.concordia.ca';
$dbname = 'ytc353_1';
$username = 'ytc353_1';
$password = 'Adm1n001';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test query
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>Tables in database:</strong></p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>🏠 Go to Main Application</a></p>";
?> 