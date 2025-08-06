<?php
// Database configuration for Local Development
$host = 'localhost'; // or '127.0.0.1'
$dbname = 'volleyball_club';
$username = 'root';
$password = 'Radio@33';
$port = 3306;

// Application configuration
define('SITE_NAME', 'Montréal Volleyball Club - Management System (LOCAL)');
define('SITE_VERSION', '1.0.0-dev');

// Error reporting (more verbose for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Session configuration
session_start();

// Timezone
date_default_timezone_set('America/Montreal');

// Database connection function
function getDBConnection() {
    global $host, $dbname, $username, $password, $port;
    
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Helper functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function formatDate($date) {
    return date('Y-m-d', strtotime($date));
}

function formatDateTime($datetime) {
    return date('Y-m-d H:i:s', strtotime($datetime));
}

// Development helper functions
function debugLog($message, $data = null) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message";
    if ($data !== null) {
        $logMessage .= " | Data: " . print_r($data, true);
    }
    error_log($logMessage . "\n", 3, __DIR__ . '/debug.log');
}

// Test database connection on include
try {
    $testConnection = getDBConnection();
    echo "<div style='background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb; border-radius: 4px;'>";
    echo "✅ Successfully connected to local database: $dbname";
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 4px;'>";
    echo "❌ Failed to connect to local database: " . $e->getMessage();
    echo "</div>";
}
?>
