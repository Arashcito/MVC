<?php
// Test database connection
$host = 'ytc353.encs.concordia.ca';
$dbname = 'ytc353_1';
$username = 'ytc353_1';
$password = 'Adm1n001';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful!\n";
    
    // Check tables in the database
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "📋 Tables in database '$dbname':\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
        
        // Show table structure
        $stmt2 = $pdo->query("DESCRIBE `$table`");
        $columns = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        echo "    Columns:\n";
        foreach ($columns as $column) {
            echo "      - {$column['Field']} ({$column['Type']})\n";
        }
        echo "\n";
    }
    
} catch(PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
}
?> 