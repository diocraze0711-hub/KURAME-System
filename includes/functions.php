<?php
function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect($location) {
    header('Location: ' . $location);
    exit;
}

function currentUser(PDO $pdo) {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireRole(PDO $pdo, array $roles) {
    $user = currentUser($pdo);
    if (!$user || !in_array($user['role'], $roles, true)) {
        redirect('dashboard.php');
    }
}

function addAlert(PDO $pdo, $message, $severity = 'warning') {
    $stmt = $pdo->prepare('INSERT INTO alerts (message, severity) VALUES (?, ?)');
    $stmt->execute([$message, $severity]);
}

function findRiskKeywords($text) {
    $keywords = [
        'suicide', 'self-harm', 'abuse', 'depression', 'harassment',
        'assault', 'attack', 'panic', 'unsafe', 'threat', 'violence', 'danger'
    ];

    $hits = [];
    $lower = strtolower($text);

    foreach ($keywords as $keyword) {
        if (stripos($lower, $keyword) !== false) {
            $hits[] = $keyword;
        }
    }

    return array_values(array_unique($hits));
}

function riskLevel($text) {
    $score = 0;
    $map = [
        'suicide' => 5,
        'self-harm' => 5,
        'assault' => 5,
        'attack' => 5,
        'abuse' => 4,
        'depression' => 4,
        'harassment' => 4,
        'threat' => 4,
        'violence' => 4,
        'panic' => 3,
        'unsafe' => 3,
        'danger' => 3,
    ];

    $lower = strtolower($text);
    foreach ($map as $keyword => $value) {
        if (stripos($lower, $keyword) !== false) {
            $score += $value;
        }
    }

    if ($score >= 12) {
        return 'critical';
    }
    if ($score >= 7) {
        return 'high';
    }
    if ($score >= 3) {
        return 'medium';
    }

    return 'low';
}

function filterProfanity($text) {
    $badWords = ['badword', 'stupid', 'idiot', 'dumb', 'foolish', 'hate'];
    foreach ($badWords as $word) {
        $text = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', '***', $text);
    }
    return $text;
}

function encryptText($text) {
    $iv = substr(hash('sha256', ENCRYPTION_KEY), 0, 16);
    return base64_encode(openssl_encrypt($text, 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv));
}

function decryptText($cipherText) {
    $iv = substr(hash('sha256', ENCRYPTION_KEY), 0, 16);
    return openssl_decrypt(base64_decode($cipherText), 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);
}

function getUserName(PDO $pdo, $userId) {
    $stmt = $pdo->prepare('SELECT username FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    return $row ? $row['username'] : 'Unknown User';
}

function statusBadge($status) {
    $colors = [
        'pending' => 'warning',
        'under review' => 'info',
        'in progress' => 'primary',
        'resolved' => 'success',
        'closed' => 'secondary',
    ];

    return '<span class="badge badge-' . ($colors[$status] ?? 'secondary') . '">' . ucfirst($status) . '</span>';
}

function displayRiskBadge($risk) {
    $class = 'badge-' . ($risk === 'critical' ? 'danger' : ($risk === 'high' ? 'warning' : ($risk === 'medium' ? 'info' : 'success')));
    return '<span class="badge ' . $class . '">' . ucfirst($risk) . ' Risk</span>';
}
