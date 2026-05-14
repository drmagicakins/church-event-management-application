-- ============================================================================
-- Team Praise Official - Ministry Event Management Platform
-- MySQL 8 Database Schema with relationships, indexes, and seed data
-- ============================================================================
-- Usage:
--   1. Log in to MySQL:  mysql -u root -p
--   2. Source this file: SOURCE /path/to/schema.sql;
-- Or import via phpMyAdmin / cPanel.
-- ============================================================================

CREATE DATABASE IF NOT EXISTS teampraise_app
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE teampraise_app;

-- ----------------------------------------------------------------------------
-- 1. ADMINS - Multi-admin support with role-based access
-- ----------------------------------------------------------------------------
CREATE TABLE admins (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(120) NOT NULL,
    email         VARCHAR(180) NOT NULL UNIQUE,
    username      VARCHAR(80)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('super_admin','manager','editor','finance','moderator')
                  NOT NULL DEFAULT 'editor',
    status        ENUM('active','suspended','blocked') NOT NULL DEFAULT 'active',
    avatar        VARCHAR(255) NULL,
    remember_token VARCHAR(120) NULL,
    last_login_at  DATETIME NULL,
    last_login_ip  VARCHAR(64) NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_admins_role_status (role, status),
    INDEX idx_admins_email (email)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 2. USERS - Public members / event attendees
-- ----------------------------------------------------------------------------
CREATE TABLE users (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(120) NOT NULL,
    email         VARCHAR(180) NOT NULL UNIQUE,
    phone         VARCHAR(40) NULL,
    password_hash VARCHAR(255) NULL,
    status        ENUM('active','suspended','blocked') NOT NULL DEFAULT 'active',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_status (status)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 3. EVENTS - Upcoming / past events
-- ----------------------------------------------------------------------------
CREATE TABLE events (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(180) NOT NULL,
    slug        VARCHAR(220) NOT NULL UNIQUE,
    theme       VARCHAR(180) NULL,
    description TEXT NOT NULL,
    event_date  DATETIME NOT NULL,
    end_date    DATETIME NULL,
    venue       VARCHAR(220) NOT NULL,
    city        VARCHAR(120) NULL,
    flyer       VARCHAR(255) NULL,
    capacity    INT UNSIGNED DEFAULT 0,
    status      ENUM('draft','published','past','cancelled') NOT NULL DEFAULT 'draft',
    created_by  BIGINT UNSIGNED NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_events_date_status (event_date, status),
    FULLTEXT INDEX ft_events_search (title, theme, description, venue)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 4. EVENT REGISTRATIONS - Attendees, ticket codes, status
-- ----------------------------------------------------------------------------
CREATE TABLE event_registrations (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id    BIGINT UNSIGNED NOT NULL,
    user_id     BIGINT UNSIGNED NULL,
    full_name   VARCHAR(140) NOT NULL,
    email       VARCHAR(180) NOT NULL,
    phone       VARCHAR(40) NULL,
    ministry    VARCHAR(150) NULL,
    ticket_code VARCHAR(80) NOT NULL UNIQUE,
    status      ENUM('pending','confirmed','checked_in','cancelled')
                NOT NULL DEFAULT 'confirmed',
    ip_address  VARCHAR(64) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE SET NULL,
    INDEX idx_reg_event_status (event_id, status),
    INDEX idx_reg_email (email)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 5. SERMON CATEGORIES
-- ----------------------------------------------------------------------------
CREATE TABLE sermon_categories (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL UNIQUE,
    slug       VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 6. SERMONS - audio / video / youtube embed
-- ----------------------------------------------------------------------------
CREATE TABLE sermons (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id  BIGINT UNSIGNED NULL,
    title        VARCHAR(190) NOT NULL,
    slug         VARCHAR(220) NOT NULL UNIQUE,
    speaker      VARCHAR(140) NOT NULL,
    sermon_date  DATE NOT NULL,
    media_type   ENUM('audio','video','youtube') NOT NULL,
    media_url    VARCHAR(255) NOT NULL,
    download_url VARCHAR(255) NULL,
    description  TEXT NULL,
    downloads    INT UNSIGNED DEFAULT 0,
    status       ENUM('draft','published') NOT NULL DEFAULT 'published',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES sermon_categories(id) ON DELETE SET NULL,
    INDEX idx_sermons_date_status (sermon_date, status),
    FULLTEXT INDEX ft_sermons_search (title, speaker, description)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 7. BLOG CATEGORIES
-- ----------------------------------------------------------------------------
CREATE TABLE blog_categories (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL UNIQUE,
    slug       VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 8. BLOG POSTS - Rich text, scheduled publishing, tags (JSON)
-- ----------------------------------------------------------------------------
CREATE TABLE blog_posts (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id     BIGINT UNSIGNED NULL,
    admin_id        BIGINT UNSIGNED NULL,
    title           VARCHAR(220) NOT NULL,
    slug            VARCHAR(240) NOT NULL UNIQUE,
    excerpt         VARCHAR(320) NULL,
    body            MEDIUMTEXT NOT NULL,
    featured_image  VARCHAR(255) NULL,
    tags            JSON NULL,
    status          ENUM('draft','scheduled','published','archived')
                    NOT NULL DEFAULT 'draft',
    published_at    DATETIME NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id)    REFERENCES admins(id)          ON DELETE SET NULL,
    INDEX idx_blog_status_published (status, published_at),
    FULLTEXT INDEX ft_blog_search (title, excerpt, body)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 9. GALLERY - images and videos
-- ----------------------------------------------------------------------------
CREATE TABLE gallery (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(160) NOT NULL,
    category        VARCHAR(100) NOT NULL,
    media_type      ENUM('image','video') NOT NULL DEFAULT 'image',
    file_path       VARCHAR(255) NOT NULL,
    thumbnail_path  VARCHAR(255) NULL,
    alt_text        VARCHAR(180) NULL,
    status          ENUM('draft','published') NOT NULL DEFAULT 'published',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_gallery_category_status (category, status)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 10. DONATIONS - Payment records
-- ----------------------------------------------------------------------------
CREATE TABLE donations (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    donor_name      VARCHAR(140) NOT NULL,
    donor_email     VARCHAR(180) NULL,
    amount          DECIMAL(12,2) NOT NULL,
    currency        CHAR(3) NOT NULL DEFAULT 'NGN',
    method          ENUM('bank_transfer','card','cash','ussd','paypal') NOT NULL,
    transaction_ref VARCHAR(120) NULL UNIQUE,
    purpose         VARCHAR(160) NULL,
    status          ENUM('pending','paid','failed','refunded')
                    NOT NULL DEFAULT 'pending',
    ip_address      VARCHAR(64) NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_don_status_date (status, created_at),
    INDEX idx_don_email (donor_email)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 11. PRAYER REQUESTS - public/private, moderated
-- ----------------------------------------------------------------------------
CREATE TABLE prayer_requests (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(140) NOT NULL,
    email         VARCHAR(180) NULL,
    request       TEXT NOT NULL,
    visibility    ENUM('public','private') NOT NULL DEFAULT 'private',
    status        ENUM('pending','approved','answered','rejected')
                  NOT NULL DEFAULT 'pending',
    moderated_by  BIGINT UNSIGNED NULL,
    ip_address    VARCHAR(64) NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (moderated_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_prayers_status_vis (status, visibility)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 12. CONTACT MESSAGES - Inbox with reply workflow
-- ----------------------------------------------------------------------------
CREATE TABLE contact_messages (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(140) NOT NULL,
    email       VARCHAR(180) NOT NULL,
    phone       VARCHAR(40) NULL,
    subject     VARCHAR(180) NOT NULL,
    message     TEXT NOT NULL,
    status      ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
    replied_by  BIGINT UNSIGNED NULL,
    ip_address  VARCHAR(64) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (replied_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_contact_status_date (status, created_at)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 13. SUBSCRIBERS - Newsletter list
-- ----------------------------------------------------------------------------
CREATE TABLE subscribers (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email               VARCHAR(180) NOT NULL UNIQUE,
    name                VARCHAR(120) NULL,
    status              ENUM('active','unsubscribed','bounced')
                        NOT NULL DEFAULT 'active',
    confirmation_token  VARCHAR(120) NULL,
    subscribed_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sub_status (status)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 14. SETTINGS - Key/value store (some JSON)
-- ----------------------------------------------------------------------------
CREATE TABLE settings (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key   VARCHAR(120) NOT NULL UNIQUE,
    setting_value LONGTEXT NULL,
    is_json       TINYINT(1) NOT NULL DEFAULT 0,
    updated_by    BIGINT UNSIGNED NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 15. ACTIVITY LOGS - Audit trail
-- ----------------------------------------------------------------------------
CREATE TABLE activity_logs (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id   BIGINT UNSIGNED NULL,
    action     VARCHAR(160) NOT NULL,
    module     VARCHAR(80) NOT NULL,
    ip_address VARCHAR(64) NULL,
    user_agent VARCHAR(255) NULL,
    metadata   JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_log_admin_date (admin_id, created_at),
    INDEX idx_log_module (module)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------------
-- 16. NOTIFICATIONS - Real-time admin notifications
-- ----------------------------------------------------------------------------
CREATE TABLE notifications (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id   BIGINT UNSIGNED NULL,
    title      VARCHAR(160) NOT NULL,
    body       VARCHAR(320) NOT NULL,
    type       ENUM('info','success','warning','danger') NOT NULL DEFAULT 'info',
    read_at    DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX idx_notif_admin_read (admin_id, read_at)
) ENGINE=InnoDB;

-- ============================================================================
-- SEED DATA - Default super admin and categories
-- Default password for superadmin is: Admin@123  (change immediately!)
-- ============================================================================

INSERT INTO admins (name, email, username, password_hash, role, status) VALUES
('Super Administrator', 'admin@teampraise.org', 'superadmin',
 '$2y$12$KIXfQn8jJ9rV6mH4yL0m5eXqF2gN3bT7yD8vW1kU6pR9sA4cE5hG2',
 'super_admin', 'active');

INSERT INTO sermon_categories (name, slug) VALUES
('Revival',    'revival'),
('Worship',    'worship'),
('Leadership', 'leadership'),
('Prayer',     'prayer');

INSERT INTO blog_categories (name, slug) VALUES
('Event Update', 'event-update'),
('Devotional',   'devotional'),
('Worship',      'worship'),
('Leadership',   'leadership');

INSERT INTO settings (setting_key, setting_value, is_json) VALUES
('site_name',        'Team Praise Official', 0),
('tagline',          'One Sound. One Praise. One Kingdom.', 0),
('contact_email',    'hello@teampraise.org', 0),
('phone',            '+234 800 000 0000', 0),
('address',          'Lagos, Nigeria', 0),
('maintenance_mode', '0', 0),
('seo_title',        'Team Praise Official - Worship and Revival Movement', 0),
('seo_description',  'Team Praise Official website, event management, sermons, prayer requests and donations.', 0),
('social_links',     '{"facebook":"#","instagram":"#","youtube":"#","x":"#"}', 1),
('bank_details',     '{"bank_name":"First Bank of Nigeria","account_name":"Team Praise Ministry","account_number":"0123456789"}', 1);
