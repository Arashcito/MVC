<?php
// Database configuration for Concordia Server (ORIGINAL BACKUP)
$host = 'ytc353.encs.concordia.ca';
$dbname = 'ytc353_1';
$username = 'ytc353_1';
$password = 'Adm1n001';
$port = 3306;

// Application configuration
define('SITE_NAME', 'Montréal Volleyball Club - Management System');
define('SITE_VERSION', '1.0.0');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
?>
