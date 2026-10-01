<?php
require_once __DIR__ . '/../config.php';

$currentUser = currentUser($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a href="index.php" class="brand">KURAME<span>SYSTEM</span></a>
            <nav>
                <?php if ($currentUser): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="wall.php">Freedom Wall</a>
                    <a href="messages.php">Messages</a>
                    <a href="cases.php">Cases</a>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                <?php else: ?>
                    <a href="index.php">Home</a>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container page-content">
