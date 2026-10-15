<?php
// Extended functions for KURAME System - uploads, reactions, replies, notes

function allowedImage($filename, $tmpName, $maxSize = 4 * 1024 * 1024) {
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmpName);
    finfo_close($finfo);

    return in_array($mime, $allowed, true) && filesize($tmpName) <= $maxSize;
}

function storeUpload($file, $subdir = 'uploads') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $uploadsDir = __DIR__ . '/../uploads/' . trim($subdir, '/');
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName = bin2hex(random_bytes(8)) . '.' . $ext;
    $destination = $uploadsDir . '/' . $safeName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $safeName;
    }
    return null;
}

function savePostImages(PDO $pdo, $postId, $files) {
    $saved = [];
    foreach ($files as $index => $file) {
        if (!is_array($file['name'])) {
            // Single file structure
            $f = $file;
        } else {
            $f = [
                'name' => $file['name'][$index],
                'type' => $file['type'][$index],
                'tmp_name' => $file['tmp_name'][$index],
                'error' => $file['error'][$index],
                'size' => $file['size'][$index]
            ];
        }

        if ($f['error'] !== UPLOAD_ERR_OK) continue;
        if (!allowedImage($f['name'], $f['tmp_name'])) continue;

        $stored = storeUpload($f, 'posts');
        if ($stored) {
            $stmt = $pdo->prepare('INSERT INTO post_images (post_id, filename) VALUES (?, ?)');
            $stmt->execute([$postId, $stored]);
            $saved[] = $stored;
        }
    }
    return $saved;
}

function getPostImages(PDO $pdo, $postId) {
    $stmt = $pdo->prepare('SELECT * FROM post_images WHERE post_id = ? ORDER BY created_at ASC');
    $stmt->execute([$postId]);
    return $stmt->fetchAll();
}

function saveMessageAttachment(PDO $pdo, $messageId, $file) {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if (!allowedImage($file['name'], $file['tmp_name'])) return null;
    $stored = storeUpload($file, 'messages');
    if ($stored) {
        $stmt = $pdo->prepare('INSERT INTO message_attachments (message_id, filename, uploaded_by) VALUES (?, ?, ?)');
        $stmt->execute([$messageId, $stored, $_SESSION['user_id'] ?? null]);
        return $stored;
    }
    return null;
}

function getMessageAttachments(PDO $pdo, $messageId) {
    $stmt = $pdo->prepare('SELECT * FROM message_attachments WHERE message_id = ? ORDER BY created_at ASC');
    $stmt->execute([$messageId]);
    return $stmt->fetchAll();
}

function addReaction(PDO $pdo, $entityType, $entityId, $reactionType) {
    $userId = $_SESSION['user_id'] ?? null;

    // toggle reaction
    $stmt = $pdo->prepare('SELECT id FROM reactions WHERE entity_type = ? AND entity_id = ? AND (user_id = ? OR anonymous_id = ?) AND reaction_type = ? LIMIT 1');
    $stmt->execute([$entityType, $entityId, $userId, null, $reactionType]);
    $row = $stmt->fetch();
    if ($row) {
        // remove reaction
        $del = $pdo->prepare('DELETE FROM reactions WHERE id = ?');
        $del->execute([$row['id']]);
        return ['status' => 'removed'];
    }

    $ins = $pdo->prepare('INSERT INTO reactions (entity_type, entity_id, user_id, reaction_type) VALUES (?, ?, ?, ?)');
    $ins->execute([$entityType, $entityId, $userId, $reactionType]);
    return ['status' => 'added'];
}

function getReactions(PDO $pdo, $entityType, $entityId) {
    $stmt = $pdo->prepare('SELECT reaction_type, COUNT(*) as count FROM reactions WHERE entity_type = ? AND entity_id = ? GROUP BY reaction_type');
    $stmt->execute([$entityType, $entityId]);
    $rows = $stmt->fetchAll();
    $out = [];
    foreach ($rows as $r) {
        $out[$r['reaction_type']] = (int)$r['count'];
    }
    return $out;
}

function addReplyToComment(PDO $pdo, $commentId, $body) {
    $userId = $_SESSION['user_id'] ?? null;
    $stmt = $pdo->prepare('INSERT INTO comment_replies (comment_id, user_id, anonymous_name, body) VALUES (?, ?, ?, ?)');
    $stmt->execute([$commentId, $userId, 'Anonymous', $body]);
    return $pdo->lastInsertId();
}

function getRepliesForComment(PDO $pdo, $commentId) {
    $stmt = $pdo->prepare('SELECT * FROM comment_replies WHERE comment_id = ? ORDER BY created_at ASC');
    $stmt->execute([$commentId]);
    return $stmt->fetchAll();
}

function addUserNote(PDO $pdo, $userId, $targetType, $targetId, $note) {
    $stmt = $pdo->prepare('INSERT INTO user_notes (user_id, target_type, target_id, note) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $targetType, $targetId, $note]);
    return $pdo->lastInsertId();
}

function getUserNotes(PDO $pdo, $userId, $targetType, $targetId) {
    $stmt = $pdo->prepare('SELECT * FROM user_notes WHERE user_id = ? AND target_type = ? AND target_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId, $targetType, $targetId]);
    return $stmt->fetchAll();
}

?>
