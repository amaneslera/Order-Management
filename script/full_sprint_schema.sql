-- =========================================================
-- Coffee Kiosk Order Management - Full Sprint Schema
-- Import this file in phpMyAdmin (or mysql CLI) to create all core tables
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `employee_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `employee_db`;

-- ---------------------------------------------------------
-- USERS (required by Auth, Admin user management, logs)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','cashier') NOT NULL DEFAULT 'cashier',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- MENU ITEMS (includes Sprint inventory columns)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) UNSIGNED NOT NULL DEFAULT 10,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- ORDERS
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `status` enum('pending','paid','completed','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- ORDER ITEMS
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(11) UNSIGNED NOT NULL,
  `menu_item_id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `addons` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_menu_item_id_foreign` (`menu_item_id`),
  CONSTRAINT `order_items_order_id_foreign`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_menu_item_id_foreign`
    FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- PAYMENTS
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(11) UNSIGNED NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_order_id_foreign` (`order_id`),
  CONSTRAINT `payments_order_id_foreign`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- ACTIVITY LOGS
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- STAFF SMS LOGS (Sprint 2)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `staff_sms_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) UNSIGNED NOT NULL,
  `staff_name` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `admin_phone` varchar(20) NOT NULL,
  `status` enum('SENT','FAILED') NOT NULL DEFAULT 'FAILED',
  `error_message` text DEFAULT NULL,
  `sms_id` varchar(100) DEFAULT NULL COMMENT 'SMS API message ID',
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_sms_logs_staff_id_idx` (`staff_id`),
  KEY `staff_sms_logs_status_idx` (`status`),
  KEY `staff_sms_logs_created_at_idx` (`created_at`),
  CONSTRAINT `staff_sms_logs_staff_id_foreign`
    FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- INVENTORY LOGS (Sprint 2/3)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inventory_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_item_id` int(11) UNSIGNED NOT NULL,
  `action` enum('add','deduct','adjust','initial') NOT NULL DEFAULT 'adjust',
  `quantity_change` int(11) NOT NULL,
  `previous_stock` int(11) UNSIGNED NOT NULL,
  `new_stock` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED DEFAULT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_logs_menu_item_id_idx` (`menu_item_id`),
  KEY `inventory_logs_created_at_idx` (`created_at`),
  KEY `inventory_logs_order_id_idx` (`order_id`),
  KEY `inventory_logs_user_id_idx` (`user_id`),
  CONSTRAINT `inventory_logs_menu_item_id_foreign`
    FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_logs_order_id_foreign`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `inventory_logs_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- STOCK ALERTS (Sprint 2)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `stock_alerts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_item_id` int(11) UNSIGNED NOT NULL,
  `alert_type` enum('low_stock','out_of_stock') DEFAULT 'low_stock',
  `current_stock` int(11) UNSIGNED NOT NULL,
  `threshold` int(11) UNSIGNED NOT NULL,
  `sent_sms` tinyint(1) DEFAULT 0,
  `sent_email` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `stock_alerts_menu_item_id_idx` (`menu_item_id`),
  KEY `stock_alerts_created_at_idx` (`created_at`),
  KEY `idx_alerts_type_date` (`alert_type`, `created_at`),
  KEY `idx_alerts_sms_status` (`sent_sms`, `created_at`),
  CONSTRAINT `stock_alerts_menu_item_id_foreign`
    FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- DEFAULT LOGIN USERS (safe upsert style)
-- admin / admin123
-- cashier / cashier123
-- ---------------------------------------------------------
INSERT INTO `users` (`username`, `password`, `role`, `created_at`, `updated_at`)
SELECT 'admin', '$2y$10$xu/uosjM9aI/c5uvuGon9u4JhqiKgiwLW5h93dloiJxpEPITR1YP2', 'Admin', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `username` = 'admin');

INSERT INTO `users` (`username`, `password`, `role`, `created_at`, `updated_at`)
SELECT 'cashier', '$2y$10$n.jjeaDd1r2Oha/mr./GVORIj665nyhPuVbCVg15GX8WEVDwfixlu', 'cashier', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `username` = 'cashier');

-- Password notes:
-- admin hash above corresponds to: admin123
-- cashier hash above corresponds to: cashier123

-- =========================================================
-- END OF FILE
-- =========================================================
