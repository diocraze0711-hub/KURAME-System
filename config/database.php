<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kurame_system');
define('DB_PORT', 3306);

// Encryption Key for Messages
define('ENCRYPTION_KEY', 'your-secret-key-change-this-32chars!!');

// Session Configuration
define('SESSION_TIMEOUT', 3600);
define('SESSION_NAME', 'kurame_session');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if ($conn->connect_error) {
    die('Database Connection Failed: ' . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset('utf8mb4');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Define base URLs
define('BASE_URL', 'http://localhost/kurame-system/');
define('API_URL', BASE_URL . 'api/');

?>