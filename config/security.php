<?php
// Security Configuration

// Start secure session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set secure session cookies
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// Function to verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Function to generate random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Function to encrypt message
function encryptMessage($message, $key = ENCRYPTION_KEY) {
    $cipher = 'AES-256-CBC';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    $encrypted = openssl_encrypt($message, $cipher, $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Function to decrypt message
function decryptMessage($encrypted, $key = ENCRYPTION_KEY) {
    $cipher = 'AES-256-CBC';
    $data = base64_decode($encrypted);
    $iv = substr($data, 0, openssl_cipher_iv_length($cipher));
    $encrypted = substr($data, openssl_cipher_iv_length($cipher));
    return openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
}

// Function to check CSRF token
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $token) {
        return false;
    }
    return true;
}

// Function to generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Function to prevent XSS
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

?>