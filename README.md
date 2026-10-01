CREATE DATABASE IF NOT EXISTS kurame_system;
USE kurame_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(160) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'professional', 'moderator', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    anonymous_name VARCHAR(50) DEFAULT 'Anonymous',
    content TEXT NOT NULL,
    category VARCHAR(50) DEFAULT 'confession',
    risk_level VARCHAR(20) DEFAULT 'low',
    flagged_keywords TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NULL,
    anonymous_name VARCHAR(50) DEFAULT 'Anonymous',
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    encrypted_body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE cases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending','under review','in progress','resolved','closed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    severity ENUM('warning','alert','emergency') NOT NULL DEFAULT 'warning',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@kurame.local', '$2y$10$6jP6UmWCIwnGPKm5d8qKOO8FfA2vDj0coKg1xv5k1V0MqngvzN7Zm', 'admin'),
('moderator', 'moderator@kurame.local', '$2y$10$6jP6UmWCIwnGPKm5d8qKOO8FfA2vDj0coKg1xv5k1V0MqngvzN7Zm', 'moderator'),
('therapist', 'therapist@kurame.local', '$2y$10$6jP6UmWCIwnGPKm5d8qKOO8FfA2vDj0coKg1xv5k1V0MqngvzN7Zm', 'professional'),
('police', 'police@kurame.local', '$2y$10$6jP6UmWCIwnGPKm5d8qKOO8FfA2vDj0coKg1xv5k1V0MqngvzN7Zm', 'professional');

INSERT INTO cases (title, description, status) VALUES
('Mental Health Check', 'Anonymous report regarding depression and isolation escalated for professional review.', 'pending'),
('Harassment Concern', 'Potential harassment and abuse concerns have been reported by a community member.', 'under review');

INSERT INTO alerts (message, severity) VALUES
('High-risk keyword pattern detected. Immediate professional review required.', 'emergency'),
('Anonymous user may be experiencing harassment or emotional distress.', 'warning');

INSERT INTO posts (user_id, anonymous_name, content, category, risk_level, flagged_keywords) VALUES
(1, 'Anonymous', 'I feel isolated and hopeless. I am struggling with depression and fear of being unsafe.', 'confession', 'high', 'depression, unsafe'),
(2, 'Anonymous', 'I was stalked and harassed online. I need help but I am afraid to speak openly.', 'report', 'high', 'harassed, afraid');

INSERT INTO comments (post_id, user_id, anonymous_name, body) VALUES
(1, 3, 'Anonymous', 'You are not alone. Please reach out to a trusted professional for support.'),
(2, 4, 'Anonymous', 'We are here to help. Let us know how to support you safely.');

INSERT INTO messages (sender_id, receiver_id, encrypted_body) VALUES
(1, 3, 'N1Om3fKYP9n4V2IAQ0JjXQ=='),
(2, 4, 'O0Zd0sPAoMZy4eQWw9n3TQ==');
