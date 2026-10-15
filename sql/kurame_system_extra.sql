-- Additional tables for extended features: images, attachments, reactions, replies, notes

USE kurame_system;

-- Post Images
CREATE TABLE IF NOT EXISTS post_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    uploaded_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Message Attachments
CREATE TABLE IF NOT EXISTS message_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    uploaded_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE
);

-- Reactions (for posts, comments and messages)
CREATE TABLE IF NOT EXISTS reactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entity_type ENUM('post','comment','message') NOT NULL,
    entity_id INT NOT NULL,
    user_id INT NULL,
    anonymous_id VARCHAR(50) NULL,
    reaction_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY ux_reaction(entity_type, entity_id, user_id, anonymous_id, reaction_type)
);

-- Comment Replies (nested replies)
CREATE TABLE IF NOT EXISTS comment_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment_id INT NOT NULL,
    user_id INT NULL,
    anonymous_name VARCHAR(50) DEFAULT 'Anonymous',
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE
);

-- User Notes (personal notes on conversations/messages)
CREATE TABLE IF NOT EXISTS user_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    target_type ENUM('conversation','message','user') NOT NULL,
    target_id INT NOT NULL,
    note TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add sample post image and reaction entries placeholders (no actual files)
INSERT INTO post_images (post_id, filename, uploaded_by) SELECT id, 'sample1.jpg', 1 FROM posts LIMIT 1;
INSERT INTO reactions (entity_type, entity_id, user_id, reaction_type) SELECT 'post', id, 2, 'support' FROM posts LIMIT 1;

