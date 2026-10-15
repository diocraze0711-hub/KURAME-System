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

    $postId = $pdo->lastInsertId();

    // Handle image uploads (multiple)
    if (!empty($_FILES['images'])) {
        savePostImages($pdo, $postId, ['name' => $_FILES['images']['name'], 'type' => $_FILES['images']['type'], 'tmp_name' => $_FILES['images']['tmp_name'], 'error' => $_FILES['images']['error'], 'size' => $_FILES['images']['size']]);
    }

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_comment'])) {
    $commentId = (int)($_POST['comment_id'] ?? 0);
    $replyBody = trim($_POST['reply_body'] ?? '');
    if ($commentId > 0 && $replyBody !== '') {
        addReplyToComment($pdo, $commentId, filterProfanity($replyBody));
        $_SESSION['flash_message'] = 'Reply added.';
    }
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
            <form method="POST" enctype="multipart/form-data">
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

                <label>Attach images (optional)</label>
                <input type="file" name="images[]" accept="image/*" multiple>

                <button type="submit" class="btn btn-primary">Post Anonymously</button>
            </form>
        </section>

        <section class="post-list">
            <?php foreach ($posts as $post): ?>
                <?php $postImgs = getPostImages($pdo, $post['id']); ?>
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

                    <?php if (!empty($postImgs)): ?>
                        <div class="post-images">
                            <?php foreach ($postImgs as $img): ?>
                                <img src="uploads/posts/<?php echo e($img['filename']); ?>" alt="post image" class="post-image">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="post-actions">
                        <button class="btn react-btn" data-entity="post" data-id="<?php echo $post['id']; ?>" data-reaction="support">Support</button>
                        <button class="btn react-btn" data-entity="post" data-id="<?php echo $post['id']; ?>" data-reaction="empathy">Empathy</button>
                        <button class="btn react-btn" data-entity="post" data-id="<?php echo $post['id']; ?>" data-reaction="helpful">Helpful</button>
                    </div>

                    <?php if (!empty($post['flagged_keywords'])): ?>
                        <p class="small-note">Detected keywords: <?php echo e($post['flagged_keywords']); ?></p>
                    <?php endif; ?>

                    <div class="comment-block">
                        <?php foreach ($comments as $comment): ?>
                            <?php if ((int)$comment['post_id'] === (int)$post['id']): ?>
                                <div class="comment-item">
                                    <strong>Anonymous Support</strong>
                                    <p><?php echo e($comment['body']); ?></p>

                                    <div class="comment-actions">
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="add_comment" value="1">
                                            <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
                                            <input type="text" name="comment" placeholder="Leave a reply...">
                                            <button class="btn btn-secondary" type="submit">Reply</button>
                                        </form>

                                        <button class="btn btn-link reply-toggle" data-comment-id="<?php echo $comment['id']; ?>">Reply</button>

                                        <?php $replies = getRepliesForComment($pdo, $comment['id']); ?>
                                        <?php if (!empty($replies)): ?>
                                            <div class="replies">
                                                <?php foreach ($replies as $r): ?>
                                                    <div class="reply-item">
                                                        <strong>Anonymous</strong>
                                                        <p><?php echo e($r['body']); ?></p>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <form method="POST" class="reply-form" id="reply-form-<?php echo $comment['id']; ?>" style="display:none; margin-top:8px;">
                                            <input type="hidden" name="reply_comment" value="1">
                                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                            <input type="text" name="reply_body" placeholder="Write your reply...">
                                            <button class="btn btn-secondary" type="submit">Post Reply</button>
                                        </form>

                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <script src="assets/js/app.js"></script>
</body>
</html>
