CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(120) NOT NULL,
  `name` VARCHAR(120) DEFAULT NULL,
  `email` VARCHAR(254) NOT NULL,
  `phone` VARCHAR(40) DEFAULT NULL,
  `subject` VARCHAR(160) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('new','read','contacted','resolved','archived') NOT NULL DEFAULT 'new',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_status_created` (`status`, `created_at`),
  KEY `idx_contact_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `contact_messages`
  ADD COLUMN IF NOT EXISTS `full_name` VARCHAR(120) NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `name` VARCHAR(120) DEFAULT NULL AFTER `full_name`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS `ip_address` VARCHAR(45) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `user_agent` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `contact_messages` MODIFY `status` ENUM('unread','new','read','contacted','resolved','archived') NOT NULL DEFAULT 'new';
UPDATE `contact_messages` SET `full_name` = COALESCE(NULLIF(`full_name`, ''), `name`, 'Unknown Sender') WHERE `full_name` IS NULL OR `full_name` = '';
UPDATE `contact_messages` SET `status` = 'new' WHERE `status` = 'unread';
ALTER TABLE `contact_messages` MODIFY `status` ENUM('new','read','contacted','resolved','archived') NOT NULL DEFAULT 'new';
ALTER TABLE `contact_messages` MODIFY `email` VARCHAR(254) NOT NULL;

CREATE TABLE IF NOT EXISTS `get_involved_submissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(254) NOT NULL,
  `phone` VARCHAR(40) DEFAULT NULL,
  `location` VARCHAR(160) DEFAULT NULL,
  `involvement_type` VARCHAR(100) NOT NULL,
  `areas_of_interest` TEXT DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `status` ENUM('new','read','contacted','in_progress','closed','archived') NOT NULL DEFAULT 'new',
  `notes` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_involved_status_created` (`status`, `created_at`),
  KEY `idx_involved_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `get_involved_submissions`
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS `notes` TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `ip_address` VARCHAR(45) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `user_agent` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `get_involved_submissions` MODIFY `status` ENUM('new','read','contacted','in_progress','closed','archived') NOT NULL DEFAULT 'new';
ALTER TABLE `get_involved_submissions` MODIFY `email` VARCHAR(254) NOT NULL;

CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(254) NOT NULL,
  `status` ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
  `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `unsubscribed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_newsletter_email` (`email`),
  KEY `idx_newsletter_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;