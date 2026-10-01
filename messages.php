<?php
require_once __DIR__ . '/config.php';
requireLogin();

$currentUser = currentUser($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? 'confession';

    if ($content === '') {
        $_SESSION['error'] = 'Post content cannot be empty.';
        redirect('wall.php');
    }

    $filtered = filterProfanity($content);
    $keywords = findRiskKeywords($filtered);
    $risk = riskLevel($filtered);

    $stmt = $pdo->prepare('INSERT INTO posts (user_id, anonymous_name, content, category, risk_level, flagged_keywords) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $currentUser['id'],
        'Anonymous',
        $filtered,
        $category,
        $risk,
        implode(', ', $keywords)
    ]);

    if ($risk !== 'low' || !empty($keywords)) {
        addAlert($pdo, 'A new high-risk anonymous post was submitted and requires support review.', $risk === 'critical' ? 'emergency' : 'warning');
    }

    $_SESSION['flash_message'] = 'Your anonymous post has been shared successfully.';
    redirect('wall.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $postId = (int)($_POST['post_id'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($postId <= 0 || $comment === '') {
        $_SESSION['error'] = 'Comment is required.';
        redirect('wall.php');
    }

    $stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, anonymous_name, body) VALUES (?, ?, ?, ?)');
    $stmt->execute([$postId, $currentUser['id'], 'Anonymous', filterProfanity($comment)]);

    $_SESSION['flash_message'] = 'Comment added successfully.';
    redirect('wall.php');
}

$posts = $pdo->query('SELECT p.*, u.username FROM posts p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC')->fetchAll();
$comments = $pdo->query('SELECT * FROM comments ORDER BY created_at ASC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freedom Wall | <?php echo APP_NAME; ?></title>
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

        <section class="panel composer">
            <h3>Anonymous Freedom Wall</h3>
            <form method="POST">
                <input type="hidden" name="create_post" value="1">
                <label>Category</label>
                <select name="category">
                    <option value="confession">Confession</option>
                    <option value="concern">Concern</option>
                    <option value="experience">Experience</option>
                    <option value="report">Report</option>
                </select>

                <label>Anonymous Content</label>
                <textarea name="content" placeholder="Write your message anonymously here..." required></textarea>

                <button type="submit" class="btn btn-primary">Post Anonymously</button>
            </form>
        </section>

        <section class="post-list">
            <?php foreach ($posts as $post): ?>
                <article class="panel post-card">
                    <div class="meta-row">
                        <strong>Anonymous User</strong>
                        <small><?php echo date('M d, Y', strtotime($post['created_at'])); ?></small>
                    </div>
                    <div class="tags-row" style="margin: 12px 0;">
                        <span class="badge badge-primary"><?php echo e($post['category']); ?></span>
                        <?php echo displayRiskBadge(strtolower($post['risk_level'])); ?>
                    </div>
                    <p><?php echo e($post['content']); ?></p>
                    <?php if (!empty($post['flagged_keywords'])): ?>
                        <p class="small-note">Detected keywords: <?php echo e($post['flagged_keywords']); ?></p>
                    <?php endif; ?>

                    <div class="comment-block">
                        <?php foreach ($comments as $comment): ?>
                            <?php if ((int)$comment['post_id'] === (int)$post['id']): ?>
                                <div class="comment-item">
                                    <strong>Anonymous Support</strong>
                                    <p><?php echo e($comment['body']); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <form method="POST" class="comment-form">
                        <input type="hidden" name="add_comment" value="1">
                        <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
                        <input type="text" name="comment" placeholder="Add supportive comment..." required>
                        <button type="submit" class="btn btn-secondary">Comment</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
