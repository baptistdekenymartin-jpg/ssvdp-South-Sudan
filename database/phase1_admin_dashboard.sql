CREATE DATABASE IF NOT EXISTS `ssvdp_south_sudan` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ssvdp_south_sudan`;

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(160) NOT NULL UNIQUE,
  `username` VARCHAR(80) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `last_login` DATETIME NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(80) NOT NULL,
  `entity_type` VARCHAR(80) NOT NULL,
  `entity_id` INT UNSIGNED NULL,
  `description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_activity_created` (`created_at`),
  CONSTRAINT `fk_activity_admin_user` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `news` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` TEXT DEFAULT NULL,
  `content` LONGTEXT DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` DATETIME NULL DEFAULT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `news`
  ADD COLUMN IF NOT EXISTS `location` VARCHAR(160) NULL AFTER `category`,
  ADD COLUMN IF NOT EXISTS `is_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
  ADD COLUMN IF NOT EXISTS `archived_at` DATETIME NULL AFTER `published_at`;

CREATE TABLE IF NOT EXISTS `news_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `news_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_news_images_news` (`news_id`),
  CONSTRAINT `fk_news_images_news` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `featured_activity` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(80) NOT NULL DEFAULT 'FEATURED ACTIVITY',
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(160) DEFAULT NULL,
  `activity_date` DATE NULL DEFAULT NULL,
  `date_label` VARCHAR(80) DEFAULT NULL,
  `location` VARCHAR(160) DEFAULT NULL,
  `participants` VARCHAR(120) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `guests` VARCHAR(255) DEFAULT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `button_label` VARCHAR(120) NOT NULL DEFAULT 'Read Full Activity Report',
  `button_link` VARCHAR(255) NOT NULL DEFAULT 'news.php',
  `status` ENUM('draft','active') NOT NULL DEFAULT 'draft',
  `updated_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_featured_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gallery_albums` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `category` VARCHAR(120) NOT NULL,
  `activity_date` DATE NULL DEFAULT NULL,
  `location` VARCHAR(160) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `created_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_gallery_album_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gallery_photos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `album_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_gallery_photos_album` (`album_id`),
  CONSTRAINT `fk_gallery_photos_album` FOREIGN KEY (`album_id`) REFERENCES `gallery_albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
