-- ========================================
-- FIX MIGRATION ISSUE
-- ========================================
-- This marks the activity_logs migration as completed
-- since the table already exists in your database

-- Run this in phpMyAdmin SQL tab:
-- 1. Open phpMyAdmin
-- 2. Select 'employee_db' database
-- 3. Click 'SQL' tab
-- 4. Copy and paste the INSERT statement below
-- 5. Click 'Go'

INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`) 
VALUES ('2024-01-01-000006', 'App\\Database\\Migrations\\CreateActivityLogsTable', 'default', 'App', UNIX_TIMESTAMP(), 1);

-- After successfully running this, you can execute: php spark migrate
-- This will create the staff_sms_logs table
