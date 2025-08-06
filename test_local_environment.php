<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Database Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border: 1px solid #bee5eb; border-radius: 5px; margin: 10px 0; }
        .test-section { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 5px; }
        h1, h2 { color: #333; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🏐 Volleyball Club - Local Environment Test</h1>
    
    <div class="info">
        <strong>Testing Local Environment Setup</strong><br>
        Database: volleyball_club<br>
        Host: localhost<br>
        Username: root
    </div>

    <?php
    // Test 1: PHP Configuration
    echo "<div class='test-section'>";
    echo "<h2>1. PHP Configuration Test</h2>";
    echo "<div class='success'>✅ PHP Version: " . phpversion() . "</div>";
    
    // Check if PDO MySQL is available
    if (extension_loaded('pdo_mysql')) {
        echo "<div class='success'>✅ PDO MySQL extension is loaded</div>";
    } else {
        echo "<div class='error'>❌ PDO MySQL extension is NOT loaded</div>";
    }
    echo "</div>";

    // Test 2: Database Connection
    echo "<div class='test-section'>";
    echo "<h2>2. Database Connection Test</h2>";
    
    $host = 'localhost';
    $dbname = 'volleyball_club';
    $username = 'root';
    $password = 'Radio@33';
    $port = 3306;
    
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<div class='success'>✅ Successfully connected to database: $dbname</div>";
        
        // Test 3: Database Information
        echo "</div><div class='test-section'>";
        echo "<h2>3. Database Information</h2>";
        
        // Get MySQL version
        $stmt = $pdo->query("SELECT VERSION() as version");
        $version = $stmt->fetch();
        echo "<div class='success'>✅ MySQL Version: " . $version['version'] . "</div>";
        
        // List tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        
        if (!empty($tables)) {
            echo "<div class='success'>✅ Tables found in database:</div>";
            echo "<pre>";
            foreach ($tables as $table) {
                echo "- " . array_values($table)[0] . "\n";
            }
            echo "</pre>";
        } else {
            echo "<div class='info'>ℹ️ No tables found in database. You may need to run the database schema script.</div>";
        }
        
    } catch(PDOException $e) {
        echo "<div class='error'>❌ Connection failed: " . $e->getMessage() . "</div>";
        echo "<div class='info'>";
        echo "<strong>Possible solutions:</strong><br>";
        echo "1. Make sure MySQL is running on your system<br>";
        echo "2. Verify the database 'volleyball_club' exists<br>";
        echo "3. Check username and password are correct<br>";
        echo "4. Ensure MySQL is running on port 3306";
        echo "</div>";
    }
    echo "</div>";

    // Test 4: Configuration File Test
    echo "<div class='test-section'>";
    echo "<h2>4. Configuration Files</h2>";
    
    if (file_exists('config.local.php')) {
        echo "<div class='success'>✅ config.local.php exists</div>";
    } else {
        echo "<div class='error'>❌ config.local.php not found</div>";
    }
    
    if (file_exists('config.concordia.php')) {
        echo "<div class='success'>✅ config.concordia.php (backup) exists</div>";
    } else {
        echo "<div class='info'>ℹ️ config.concordia.php (backup) not found</div>";
    }
    echo "</div>";
    ?>

    <div class="test-section">
        <h2>5. Next Steps</h2>
        <div class="info">
            <strong>To use the local environment:</strong><br>
            1. Update your main files to include 'config.local.php' instead of 'config.php'<br>
            2. Or rename 'config.local.php' to 'config.php' to replace the current config<br>
            3. Make sure your database has the required tables (run database schema if needed)<br>
            4. Test your application with the local database
        </div>
    </div>

    <div class="test-section">
        <h2>6. Quick Commands</h2>
        <div class="info">
            <strong>Switch to local config:</strong><br>
            <pre>mv config.php config.php.backup && mv config.local.php config.php</pre>
            
            <strong>Switch back to Concordia config:</strong><br>
            <pre>mv config.php config.local.php && mv config.php.backup config.php</pre>
        </div>
    </div>

</body>
</html>
