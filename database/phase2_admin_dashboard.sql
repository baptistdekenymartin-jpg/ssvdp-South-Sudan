CREATE DATABASE IF NOT EXISTS `ssvdp_south_sudan` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ssvdp_south_sudan`;

CREATE TABLE IF NOT EXISTS `events` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `type` VARCHAR(80) NOT NULL DEFAULT 'Announcement',
  `short_description` TEXT DEFAULT NULL,
  `full_description` LONGTEXT DEFAULT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL DEFAULT NULL,
  `start_time` TIME NULL DEFAULT NULL,
  `end_time` TIME NULL DEFAULT NULL,
  `location` VARCHAR(180) DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  `created_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_events_status_date` (`status`, `start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `programme_updates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `programme` VARCHAR(160) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `update_date` DATE NOT NULL,
  `location` VARCHAR(180) DEFAULT NULL,
  `short_description` TEXT DEFAULT NULL,
  `full_description` LONGTEXT DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  `created_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_programme_updates_status_date` (`status`, `update_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `impact_updates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `value` VARCHAR(80) DEFAULT NULL,
  `unit` VARCHAR(80) DEFAULT NULL,
  `programme` VARCHAR(160) DEFAULT NULL,
  `description` TEXT NOT NULL,
  `impact_date` DATE NOT NULL,
  `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  `created_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_impact_status_date` (`status`, `impact_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `documents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `category` VARCHAR(120) DEFAULT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(80) DEFAULT NULL,
  `published_at` DATE NULL DEFAULT NULL,
  `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  `created_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_documents_status_date` (`status`, `published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `partners` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `type` ENUM('Partner','Donor') NOT NULL DEFAULT 'Partner',
  `logo_path` VARCHAR(255) DEFAULT NULL,
  `website_url` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `status` ENUM('active','hidden','archived') NOT NULL DEFAULT 'active',
  `created_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_partners_status_order` (`status`, `display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
