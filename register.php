<?php
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $_SESSION['error'] = 'Email and password are required.';
        redirect('login.php');
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['flash_message'] = 'Welcome back, ' . $user['username'] . '!';
        redirect('dashboard.php');
    }

    $_SESSION['error'] = 'Incorrect email or password.';
    redirect('login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a href="index.php" class="brand">KURAME<span>SYSTEM</span></a>
            <nav>
                <a href="index.php">Home</a>
                <a href="register.php">Register</a>
            </nav>
        </div>
    </header>

    <main class="container auth-page">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="auth-card">
            <h2>Login</h2>
            <form method="POST">
                <input type="hidden" name="login" value="1">
                <label>Email</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <button type="submit" class="btn btn-primary full">Login</button>
            </form>
            <p class="subtle">Need an account? <a href="register.php">Create one</a></p>
        </div>
    </main>
</body>
</html>
