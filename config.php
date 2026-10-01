<?php
session_start();


define('DB_HOST', 'localhost');
define('DB_NAME', 'kurame_system');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_NAME', 'KURAME SYSTEM');
define('ENCRYPTION_KEY', 'KURAME-2026-SECURE-KEY-32');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

require_once __DIR__ . '/includes/functions.php';
