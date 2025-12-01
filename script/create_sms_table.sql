-- Create staff_sms_logs table for Staff to Admin SMS messaging
-- Run this SQL in phpMyAdmin or your MySQL client

CREATE TABLE IF NOT EXISTS `staff_sms_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `staff_id` INT(11) UNSIGNED NOT NULL,
  `staff_name` VARCHAR(100) NOT NULL,
  `message` TEXT NOT NULL,
  `admin_phone` VARCHAR(20) NOT NULL,
  `status` ENUM('SENT','FAILED') NOT NULL DEFAULT 'FAILED',
  `error_message` TEXT NULL,
  `sms_id` VARCHAR(100) NULL COMMENT 'SMS API message ID',
  `sent_at` TIMESTAMP NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `staff_id` (`staff_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `staff_sms_logs_staff_id_foreign` 
    FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Verify table was created
SELECT 'staff_sms_logs table created successfully!' AS message;
