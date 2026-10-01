<?php
require_once __DIR__ . '/config.php';
requireLogin();

$currentUser = currentUser($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiverId = (int)($_POST['receiver_id'] ?? 0);
    $body = trim($_POST['body'] ?? '');

    if ($receiverId <= 0 || $body === '') {
        $_SESSION['error'] = 'A recipient and message are required.';
        redirect('messages.php');
    }

    $stmt = $pdo->prepare('INSERT INTO messages (sender_id, receiver_id, encrypted_body) VALUES (?, ?, ?)');
    $stmt->execute([$currentUser['id'], $receiverId, encryptText($body)]);

    $_SESSION['flash_message'] = 'Secure message sent.';
    redirect('messages.php');
}

$professionals = $pdo->query("SELECT * FROM users WHERE role IN ('professional', 'moderator', 'admin') ORDER BY role ASC")->fetchAll();

$stmt = $pdo->prepare('SELECT m.*, s.username AS sender_name, r.username AS receiver_name FROM messages m JOIN users s ON s.id = m.sender_id JOIN users r ON r.id = m.receiver_id WHERE m.sender_id = ? OR m.receiver_id = ? ORDER BY m.created_at DESC');
$stmt->execute([$currentUser['id'], $currentUser['id']]);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <a href="index.php" class="brand">KURAME<span>SYSTEM</span></a>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="wall.php">Freedom Wall</a>
                <a href="messages.php">Messages</a>
                <a href="cases.php">Cases</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container page-content">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo e($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-success"><?php echo e($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?></div>
        <?php endif; ?>

        <section class="two-column">
            <div class="panel">
                <h3>Send Secure Message</h3>
                <form method="POST">
                    <input type="hidden" name="send_message" value="1">
                    <label>Professional</label>
                    <select name="receiver_id">
                        <option value="">Select a support professional</option>
                        <?php foreach ($professionals as $professional): ?>
                            <?php if ((int)$professional['id'] !== (int)$currentUser['id']): ?>
                                <option value="<?php echo (int)$professional['id']; ?>"><?php echo e($professional['username']); ?> (<?php echo e($professional['role']); ?>)</option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <label>Message</label>
                    <textarea name="body" placeholder="Write a confidential message to a verified professional..." required></textarea>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>

            <div class="panel">
                <h3>Inbox</h3>
                <div class="list-compact">
                    <?php foreach ($messages as $message): ?>
                        <div class="list-item">
                            <div class="meta-row">
                                <strong><?php echo e($message['sender_name']); ?> → <?php echo e($message['receiver_name']); ?></strong>
                                <small><?php echo date('M d, Y h:i A', strtotime($message['created_at'])); ?></small>
                            </div>
                            <p><?php echo e(decryptText($message['encrypted_body'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
