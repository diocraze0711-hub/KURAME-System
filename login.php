<?php
require_once __DIR__ . '/config.php';

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
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-success"><?php echo e($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?></div>
        <?php endif; ?>

        <section class="hero-card">
            <div>
                <p class="eyebrow">WEB-BASED FREEDOM WALL & SUPPORT PLATFORM</p>
                <h1>Keeping Users Resilient through Anonymous Messaging and Engagement</h1>
                <p class="lead">
                    KURAME SYSTEM connects anonymous users with secure support channels, professional guidance,
                    and community protection. It filters risky content, detects emergencies, and routes cases to the proper service.
                </p>
                <div class="hero-actions">
                    <a href="register.php" class="btn btn-primary">Create Account</a>
                    <a href="login.php" class="btn btn-secondary">Login</a>
                </div>
            </div>
            <div class="panel stats-panel">
                <h3>System Snapshot</h3>
                <div class="mini-stat"><strong><?php echo $pdo->query('SELECT COUNT(*) AS total FROM users')->fetch()['total']; ?></strong><span>Members</span></div>
                <div class="mini-stat"><strong><?php echo $pdo->query('SELECT COUNT(*) AS total FROM posts')->fetch()['total']; ?></strong><span>Posts</span></div>
                <div class="mini-stat"><strong><?php echo $pdo->query('SELECT COUNT(*) AS total FROM cases')->fetch()['total']; ?></strong><span>Cases</span></div>
                <div class="mini-stat"><strong><?php echo $pdo->query('SELECT COUNT(*) AS total FROM alerts')->fetch()['total']; ?></strong><span>Alerts</span></div>
            </div>
        </section>

        <section class="feature-grid">
            <article class="panel">
                <h3>Anonymous Freedom Wall</h3>
                <p>Users can post concerns, confessions, and experiences anonymously while harmful language is filtered automatically.</p>
            </article>
            <article class="panel">
                <h3>Private Messaging</h3>
                <p>Users can communicate privately with verified professionals in a protected 1-on-1 conversation environment.</p>
            </article>
            <article class="panel">
                <h3>Professional Intervention</h3>
                <p>Critical content is routed to the correct role: police for crime issues and therapists for mental health support.</p>
            </article>
        </section>

        <section class="feature-grid second-grid">
            <article class="panel">
                <h3>Risk Detection & Alerts</h3>
                <p>Keywords such as suicide, abuse, depression, and harassment trigger rapid alert notices for professional review.</p>
            </article>
            <article class="panel">
                <h3>Case Tracking</h3>
                <p>Posts and concerns are tracked in statuses from pending to resolved while maintaining anonymity and confidentiality.</p>
            </article>
            <article class="panel">
                <h3>Community Support</h3>
                <p>Supportive comments and reactions promote peer help while preserving a moderated, non-toxic space.</p>
            </article>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> KURAME SYSTEM. Built for safety, support, and resilience.</p>
        </div>
    </footer>
</body>
</html>
