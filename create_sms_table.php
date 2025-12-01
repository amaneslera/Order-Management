<?php

/**
 * Create staff_sms_logs table manually
 * Run this file: php create_sms_table.php
 */

// Direct database connection using environment variables
$hostname = 'localhost';
$database = 'employee_db';
$username = 'root';
$password = '';

echo "Creating staff_sms_logs table...\n";

try {
    // Create connection
    $mysqli = new mysqli($hostname, $username, $password, $database);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✓ Connected to database: $database\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS `staff_sms_logs` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    if ($mysqli->query($sql)) {
        echo "✅ SUCCESS: staff_sms_logs table created successfully!\n";
        echo "\nTable structure:\n";
        echo "- id (PRIMARY KEY)\n";
        echo "- staff_id (FOREIGN KEY → users.id)\n";
        echo "- staff_name\n";
        echo "- message\n";
        echo "- admin_phone\n";
        echo "- status (SENT/FAILED)\n";
        echo "- error_message\n";
        echo "- sms_id\n";
        echo "- sent_at\n";
        echo "- created_at\n";
    } else {
        throw new Exception($mysqli->error);
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "ℹ️  Table already exists - no action needed.\n";
    } else {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
        echo "\nAlternative: Run the SQL file manually in phpMyAdmin:\n";
        echo "File: script/create_sms_table.sql\n";
        exit(1);
    }
}

echo "\n✅ SMS feature is ready to use!\n";
echo "\nNext steps:\n";
echo "1. Configure Semaphore API key in .env file\n";
echo "2. Login as cashier and visit: http://localhost/Order-Management/staff/send-sms\n";
echo "3. Send a test SMS to admin (phone: +639686186310)\n";
