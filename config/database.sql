-- KURAME System Database Structure

CREATE DATABASE IF NOT EXISTS kurame_system;
USE kurame_system;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'moderator', 'admin', 'police', 'therapist') NOT NULL DEFAULT 'user',
    department VARCHAR(100),
    license_number VARCHAR(50),
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX(role),
    INDEX(is_active)
);

-- Anonymous User Sessions Table
CREATE TABLE anonymous_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    user_id INT,
    anonymous_id VARCHAR(20) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX(session_token),
    INDEX(anonymous_id)
);

-- Posts Table (Freedom Wall)
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    anonymous_id VARCHAR(20) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('confession', 'concern', 'experience', 'report') DEFAULT 'confession',
    sentiment_score FLOAT,
    risk_level ENUM('low', 'medium', 'high', 'critical') DEFAULT 'low',
    flagged_keywords TEXT,
    is_flagged BOOLEAN DEFAULT FALSE,
    flagged_reason VARCHAR(255),
    is_archived BOOLEAN DEFAULT FALSE,
    views_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX(anonymous_id),
    INDEX(risk_level),
    INDEX(is_flagged),
    INDEX(created_at),
    FULLTEXT INDEX(content)
);

-- Post Reactions Table
CREATE TABLE post_reactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    anonymous_id VARCHAR(20),
    reaction_type ENUM('support', 'empathy', 'helpful', 'concerned') DEFAULT 'support',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE KEY(post_id, anonymous_id, reaction_type),
    INDEX(post_id)
);

-- Comments Table
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    anonymous_id VARCHAR(20),
    content TEXT NOT NULL,
    is_approved BOOLEAN DEFAULT TRUE,
    is_flagged BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE CASCADE,
    INDEX(post_id),
    INDEX(anonymous_id),
    FULLTEXT INDEX(content)
);

-- Private Messages Table
CREATE TABLE private_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    sender_id INT,
    sender_anonymous_id VARCHAR(20),
    receiver_id INT NOT NULL,
    receiver_anonymous_id VARCHAR(20),
    message TEXT NOT NULL,
    encrypted_message LONGTEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(conversation_id),
    INDEX(sender_id),
    INDEX(receiver_id),
    INDEX(created_at)
);

-- Conversations Table
CREATE TABLE conversations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    professional_id INT NOT NULL,
    user_anonymous_id VARCHAR(20),
    professional_role ENUM('police', 'therapist') NOT NULL,
    status ENUM('active', 'closed', 'archived') DEFAULT 'active',
    case_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(professional_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX(user_id),
    INDEX(professional_id),
    INDEX(status)
);

-- Cases/Tickets Table
CREATE TABLE cases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    case_number VARCHAR(50) UNIQUE NOT NULL,
    post_id INT,
    conversation_id INT,
    assigned_to INT,
    category ENUM('crime', 'mental_health', 'harassment', 'abuse', 'other') NOT NULL,
    status ENUM('pending', 'under_review', 'in_progress', 'resolved', 'closed') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'low',
    description TEXT,
    resolution_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE SET NULL,
    FOREIGN KEY(conversation_id) REFERENCES conversations(id) ON DELETE SET NULL,
    FOREIGN KEY(assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX(case_number),
    INDEX(status),
    INDEX(assigned_to),
    INDEX(category)
);

-- Case Activity Log
CREATE TABLE case_activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    case_id INT NOT NULL,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(case_id) REFERENCES cases(id) ON DELETE CASCADE,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX(case_id),
    INDEX(created_at)
);

-- Profanity Filter List
CREATE TABLE profanity_filters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    word VARCHAR(100) NOT NULL,
    replacement VARCHAR(100),
    severity ENUM('low', 'medium', 'high') DEFAULT 'low',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY(word),
    INDEX(is_active)
);

-- Sensitive Keywords Table
CREATE TABLE sensitive_keywords (
    id INT PRIMARY KEY AUTO_INCREMENT,
    keyword VARCHAR(100) NOT NULL,
    category ENUM('suicide', 'abuse', 'mental_health', 'crime', 'harassment', 'drug') NOT NULL,
    risk_level ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY(keyword),
    INDEX(category),
    INDEX(is_active)
);

-- Alerts Table
CREATE TABLE alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT,
    conversation_id INT,
    alert_type ENUM('high_risk', 'critical_emergency', 'abuse_detected', 'crime_report') NOT NULL,
    severity ENUM('warning', 'alert', 'emergency') DEFAULT 'alert',
    message TEXT,
    assigned_to INT,
    status ENUM('pending', 'acknowledged', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    acknowledged_at TIMESTAMP NULL,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY(post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY(conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY(assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX(alert_type),
    INDEX(status),
    INDEX(severity),
    INDEX(created_at)
);

-- Audit Log Table
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    old_value LONGTEXT,
    new_value LONGTEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX(user_id),
    INDEX(action),
    INDEX(created_at)
);

-- Emergency Contacts Table
CREATE TABLE emergency_contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    website VARCHAR(255),
    email VARCHAR(100),
    category ENUM('mental_health', 'crisis', 'abuse', 'police', 'general') NOT NULL,
    country VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(category),
    INDEX(is_active)
);

-- System Settings
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value LONGTEXT,
    setting_type ENUM('text', 'number', 'boolean', 'array') DEFAULT 'text',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'KURAME System', 'text', 'Website name'),
('site_description', 'Freedom Wall with Integrated Private Messaging', 'text', 'Website description'),
('enable_registration', '1', 'boolean', 'Allow user registration'),
('enable_anonymous_posts', '1', 'boolean', 'Allow anonymous posts'),
('max_post_length', '5000', 'number', 'Maximum post length'),
('post_moderation_enabled', '1', 'boolean', 'Enable post moderation'),
('sentiment_analysis_enabled', '1', 'boolean', 'Enable sentiment analysis'),
('auto_flag_high_risk', '1', 'boolean', 'Auto-flag high risk posts');

-- Insert sample profanity filters
INSERT INTO profanity_filters (word, replacement, severity) VALUES
('badword1', '***', 'high'),
('offensive1', '***', 'medium'),
('insult1', '***', 'medium');

-- Insert sensitive keywords
INSERT INTO sensitive_keywords (keyword, category, risk_level) VALUES
('suicide', 'suicide', 'critical'),
('self-harm', 'suicide', 'critical'),
('abuse', 'abuse', 'high'),
('domestic violence', 'abuse', 'high'),
('depression', 'mental_health', 'high'),
('harassment', 'harassment', 'high'),
('assault', 'crime', 'critical'),
('drug', 'drug', 'high');

-- Insert emergency contacts
INSERT INTO emergency_contacts (name, phone, category, country) VALUES
('National Suicide Prevention Lifeline', '1-800-273-8255', 'crisis', 'USA'),
('Crisis Text Line', 'Text HOME to 741741', 'crisis', 'USA'),
('National Domestic Violence Hotline', '1-800-799-7233', 'abuse', 'USA'),
('Emergency Services', '911', 'police', 'USA');

-- Create indexes for performance
CREATE INDEX idx_posts_risk_level_created ON posts(risk_level, created_at);
CREATE INDEX idx_posts_anonymous_created ON posts(anonymous_id, created_at);
CREATE INDEX idx_messages_conversation_created ON private_messages(conversation_id, created_at);
CREATE INDEX idx_cases_status_assigned ON cases(status, assigned_to);
CREATE INDEX idx_alerts_status_severity ON alerts(status, severity);