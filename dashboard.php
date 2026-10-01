<?php
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if ($username === '' || $email === '' || $password === '') {
        $_SESSION['error'] = 'All fields are required.';
        redirect('register.php');
    }

    $allowedRoles = ['user', 'professional', 'moderator', 'admin'];
    if (!in_array($role, $allowedRoles, true)) {
        $role = 'user';
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1');
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Email or username already exists.';
        redirect('register.php');
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $insert = $pdo->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
    $insert->execute([$username, $email, $hash, $role]);

    $_SESSION['flash_message'] = 'Registration successful. Please log in.';
    redirect('login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a href="index.php" class="brand">KURAME<span>SYSTEM</span></a>
            <nav>
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
            </nav>
        </div>
    </header>

    <main class="container auth-page">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="auth-card">
            <h2>Create Account</h2>
            <form method="POST">
                <input type="hidden" name="register" value="1">
                <label>Username</label>
                <input type="text" name="username" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <label>Role</label>
                <select name="role">
                    <option value="user">User</option>
                    <option value="professional">Professional</option>
                    <option value="moderator">Moderator</option>
                    <option value="admin">Admin</option>
                </select>

                <button type="submit" class="btn btn-primary full">Register</button>
            </form>
            <p class="subtle">Already registered? <a href="login.php">Login here</a></p>
        </div>
    </main>
</body>
</html>
