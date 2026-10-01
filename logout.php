<?php
require_once __DIR__ . '/config.php';
requireLogin();

$currentUser = currentUser($pdo);
if (!in_array($currentUser['role'], ['professional', 'moderator', 'admin'], true)) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_case'])) {
    $caseId = (int)($_POST['case_id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';

    if ($caseId > 0) {
        $stmt = $pdo->prepare('UPDATE cases SET status = ? WHERE id = ?');
        $stmt->execute([$status, $caseId]);
        $_SESSION['flash_message'] = 'Case status updated.';
        redirect('cases.php');
    }
}

$cases = $pdo->query('SELECT * FROM cases ORDER BY created_at DESC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cases | <?php echo APP_NAME; ?></title>
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
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-success"><?php echo e($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?></div>
        <?php endif; ?>

        <section class="panel">
            <h3>Case Tracking</h3>
            <?php foreach ($cases as $case): ?>
                <div class="case-item">
                    <div class="meta-row">
                        <strong><?php echo e($case['title']); ?></strong>
                        <?php echo statusBadge($case['status']); ?>
                    </div>
                    <p><?php echo e($case['description']); ?></p>

                    <form method="POST" class="case-update">
                        <input type="hidden" name="update_case" value="1">
                        <input type="hidden" name="case_id" value="<?php echo (int)$case['id']; ?>">
                        <select name="status">
                            <option value="pending" <?php echo $case['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="under review" <?php echo $case['status'] === 'under review' ? 'selected' : ''; ?>>Under Review</option>
                            <option value="in progress" <?php echo $case['status'] === 'in progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="resolved" <?php echo $case['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                        </select>
                        <button type="submit" class="btn btn-warning">Update</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
