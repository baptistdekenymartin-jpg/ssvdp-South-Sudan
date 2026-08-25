ALTER TABLE `admin_users`
  ADD COLUMN IF NOT EXISTS `role` ENUM('administrator','editor') NOT NULL DEFAULT 'administrator' AFTER `password_hash`,
  ADD COLUMN IF NOT EXISTS `two_factor_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `last_login`,
  ADD COLUMN IF NOT EXISTS `two_factor_secret` VARCHAR(255) NULL AFTER `two_factor_enabled`,
  ADD COLUMN IF NOT EXISTS `password_changed_at` DATETIME NULL AFTER `two_factor_secret`;

UPDATE `admin_users` SET `status` = 'disabled' WHERE `status` = 'inactive';
ALTER TABLE `admin_users` MODIFY `status` ENUM('active','disabled') NOT NULL DEFAULT 'active';

ALTER TABLE `admin_activity_log`
  ADD COLUMN IF NOT EXISTS `ip_address` VARCHAR(45) NULL AFTER `description`;

CREATE TABLE IF NOT EXISTS `admin_login_attempts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `identifier` VARCHAR(160) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `success` TINYINT(1) NOT NULL DEFAULT 0,
  `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_login_attempts_lookup` (`identifier`, `ip_address`, `attempted_at`),
  KEY `idx_login_attempts_success` (`success`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin_security_settings` (
  `setting_key` VARCHAR(80) NOT NULL,
  `setting_value` VARCHAR(255) NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admin_security_settings` (`setting_key`, `setting_value`) VALUES
('session_idle_minutes', '45'),
('session_absolute_hours', '8'),
('login_failed_limit', '5'),
('login_window_minutes', '15')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);