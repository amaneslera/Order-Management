<?php

/**
 * Create stock_alerts table manually
 * Run this file: php create_stock_alerts_table.php
 */

$hostname = 'localhost';
$database = 'employee_db';
$username = 'root';
$password = '';

echo "Creating stock_alerts table...\n";

try {
    $mysqli = new mysqli($hostname, $username, $password, $database);

    if ($mysqli->connect_error) {
        throw new Exception('Connection failed: ' . $mysqli->connect_error);
    }

    echo "Connected to database: {$database}\n";

    $sql = "CREATE TABLE IF NOT EXISTS `stock_alerts` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `menu_item_id` INT(11) UNSIGNED NOT NULL,
      `alert_type` ENUM('low_stock','out_of_stock') DEFAULT 'low_stock',
      `current_stock` INT(11) UNSIGNED NOT NULL,
      `threshold` INT(11) UNSIGNED NOT NULL,
      `sent_sms` TINYINT(1) DEFAULT 0,
      `sent_email` TINYINT(1) DEFAULT 0,
      `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `stock_alerts_menu_item_id_idx` (`menu_item_id`),
      KEY `stock_alerts_created_at_idx` (`created_at`),
      KEY `idx_alerts_type_date` (`alert_type`, `created_at`),
      KEY `idx_alerts_sms_status` (`sent_sms`, `created_at`),
      CONSTRAINT `stock_alerts_menu_item_id_foreign`
        FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if (!$mysqli->query($sql)) {
        throw new Exception($mysqli->error);
    }

    echo "SUCCESS: stock_alerts table is ready.\n";

    $mysqli->close();
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
    exit(1);
}
