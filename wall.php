<?php
require_once __DIR__ . '/config.php';
requireLogin();

$currentUser = currentUser($pdo);

$stats = [
    'users' => $pdo->query('SELECT COUNT(*) AS total FROM users')->fetch()['total'],
    'posts' => $pdo->query('SELECT COUNT(*) AS total FROM posts')->fetch()['total'],
    'cases' => $pdo->query('SELECT COUNT(*) AS total FROM cases')->fetch()['total'],
    'alerts' => $pdo->query('SELECT COUNT(*) AS total FROM alerts')->fetch()['total'],
];

$posts = $pdo->query('SELECT p.*, u.username FROM posts p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 10')->fetchAll();
$cases = $pdo->query('SELECT * FROM cases ORDER BY created_at DESC LIMIT 10')->fetchAll();
$alerts = $pdo->query('SELECT * FROM alerts ORDER BY created_at DESC LIMIT 5')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?php echo APP_NAME; ?></title>
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

        <section class="welcome-bar panel">
            <div>
                <p class="eyebrow">WELCOME</p>
                <h2><?php echo e($currentUser['username']); ?></h2>
            </div>
            <div class="inline-role">
                <span class="badge badge-primary"><?php echo ucfirst($currentUser['role']); ?></span>
            </div>
        </section>

        <section class="stats-grid">
            <div class="panel stat-box">
                <span>Users</span>
                <strong><?php echo $stats['users']; ?></strong>
            </div>
            <div class="panel stat-box">
                <span>Posts</span>
                <strong><?php echo $stats['posts']; ?></strong>
            </div>
            <div class="panel stat-box">
                <span>Cases</span>
                <strong><?php echo $stats['cases']; ?></strong>
            </div>
            <div class="panel stat-box">
                <span>Alerts</span>
                <strong><?php echo $stats['alerts']; ?></strong>
            </div>
        </section>

        <section class="two-column">
            <div class="panel">
                <h3>Recent Posts</h3>
                <div class="list-compact">
                    <?php foreach ($posts as $post): ?>
                        <div class="list-item">
                            <div class="meta-row">
                                <strong>Anonymous</strong>
                                <small><?php echo date('M d, Y', strtotime($post['created_at'])); ?></small>
                            </div>
                            <p><?php echo e($post['content']); ?></p>
                            <div class="tags-row">
                                <span class="badge badge-primary"><?php echo e($post['category']); ?></span>
                                <?php echo displayRiskBadge(strtolower($post['risk_level'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="panel">
                <h3>Alerts</h3>
                <div class="list-compact">
                    <?php foreach ($alerts as $alert): ?>
                        <div class="list-item danger-item">
                            <div class="meta-row">
                                <strong><?php echo ucfirst($alert['severity']); ?></strong>
                                <small><?php echo date('M d, Y', strtotime($alert['created_at'])); ?></small>
                            </div>
                            <p><?php echo e($alert['message']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="panel" style="margin-top: 24px;">
            <h3>Open Cases</h3>
            <div class="list-compact">
                <?php foreach ($cases as $case): ?>
                    <div class="list-item">
                        <div class="meta-row">
                            <strong><?php echo e($case['title']); ?></strong>
                            <?php echo statusBadge($case['status']); ?>
                        </div>
                        <p><?php echo e($case['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>
</html>
