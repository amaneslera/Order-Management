-- =========================================================
-- Coffee Kiosk Order Management - Demo Seed Data
-- Run this AFTER importing: script/full_sprint_schema.sql
-- Safe to rerun: it cleans and recreates only demo rows by ID ranges.
-- =========================================================

USE `employee_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- Clean existing demo rows (only reserved demo ID ranges)
DELETE FROM `inventory_logs` WHERE `id` >= 950000;
DELETE FROM `stock_alerts` WHERE `id` >= 960000;
DELETE FROM `staff_sms_logs` WHERE `id` >= 930000;
DELETE FROM `activity_logs` WHERE `id` >= 940000;
DELETE FROM `payments` WHERE `id` >= 920000;
DELETE FROM `order_items` WHERE `id` >= 910000;
DELETE FROM `orders` WHERE `id` >= 900000;
DELETE FROM `menu_items` WHERE `id` BETWEEN 1001 AND 1010;

SET FOREIGN_KEY_CHECKS = 1;

-- Ensure demo staff accounts exist
INSERT INTO `users` (`username`, `password`, `role`, `created_at`, `updated_at`)
SELECT 'admin', '$2y$10$xu/uosjM9aI/c5uvuGon9u4JhqiKgiwLW5h93dloiJxpEPITR1YP2', 'Admin', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `username` = 'admin');

INSERT INTO `users` (`username`, `password`, `role`, `created_at`, `updated_at`)
SELECT 'cashier', '$2y$10$n.jjeaDd1r2Oha/mr./GVORIj665nyhPuVbCVg15GX8WEVDwfixlu', 'cashier', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `username` = 'cashier');

SET @admin_id := (SELECT `id` FROM `users` WHERE `username` = 'admin' LIMIT 1);
SET @cashier_id := (SELECT `id` FROM `users` WHERE `username` = 'cashier' LIMIT 1);

-- Demo menu catalog (with inventory values)
INSERT INTO `menu_items`
(`id`, `name`, `category`, `description`, `price`, `stock_quantity`, `low_stock_threshold`, `image`, `status`, `created_at`, `updated_at`)
VALUES
(1001, 'Espresso', 'Coffee', 'Strong and bold espresso shot', 80.00, 25, 10, 'espresso.jpg', 'available', NOW(), NOW()),
(1002, 'Americano', 'Coffee', 'Espresso with hot water', 95.00, 22, 10, 'americano.jpg', 'available', NOW(), NOW()),
(1003, 'Cappuccino', 'Coffee', 'Espresso with steamed milk foam', 120.00, 7, 8, 'cappuccino.jpg', 'available', NOW(), NOW()),
(1004, 'Latte', 'Coffee', 'Espresso with steamed milk', 130.00, 5, 8, 'latte.jpg', 'available', NOW(), NOW()),
(1005, 'Mocha', 'Coffee', 'Chocolate flavored latte', 140.00, 0, 8, 'mocha.jpg', 'available', NOW(), NOW()),
(1006, 'Caramel Macchiato', 'Coffee', 'Vanilla latte with caramel drizzle', 150.00, 14, 10, 'caramel_macchiato.jpg', 'available', NOW(), NOW()),
(1007, 'Chocolate Chip Cookie', 'Snacks', 'Freshly baked cookie', 45.00, 35, 12, 'cookie.jpg', 'available', NOW(), NOW()),
(1008, 'Blueberry Muffin', 'Snacks', 'Soft blueberry muffin', 65.00, 11, 10, 'muffin.jpg', 'available', NOW(), NOW()),
(1009, 'Croissant', 'Snacks', 'Buttery flaky croissant', 75.00, 9, 10, 'croissant.jpg', 'available', NOW(), NOW()),
(1010, 'Ham and Cheese Sandwich', 'Snacks', 'Toasted sandwich', 95.00, 4, 6, 'sandwich.jpg', 'available', NOW(), NOW());

-- Demo orders
INSERT INTO `orders`
(`id`, `order_number`, `status`, `total_amount`, `created_at`, `updated_at`)
VALUES
(900001, 'ORD-DEMO-0001', 'paid', 295.00, NOW() - INTERVAL 2 HOUR, NOW() - INTERVAL 2 HOUR),
(900002, 'ORD-DEMO-0002', 'completed', 185.00, NOW() - INTERVAL 90 MINUTE, NOW() - INTERVAL 60 MINUTE),
(900003, 'ORD-DEMO-0003', 'pending', 215.00, NOW() - INTERVAL 20 MINUTE, NOW() - INTERVAL 20 MINUTE),
(900004, 'ORD-DEMO-0004', 'cancelled', 140.00, NOW() - INTERVAL 15 MINUTE, NOW() - INTERVAL 10 MINUTE);

-- Demo order items
INSERT INTO `order_items`
(`id`, `order_id`, `menu_item_id`, `quantity`, `price`, `addons`, `notes`)
VALUES
(910001, 900001, 1001, 1, 80.00, '', ''),
(910002, 900001, 1002, 1, 95.00, '', ''),
(910003, 900001, 1003, 1, 120.00, '', ''),
(910004, 900002, 1007, 1, 45.00, '', ''),
(910005, 900002, 1008, 1, 65.00, '', ''),
(910006, 900002, 1009, 1, 75.00, '', ''),
(910007, 900003, 1004, 1, 130.00, '', 'Less sugar'),
(910008, 900003, 1010, 1, 95.00, '', ''),
(910009, 900004, 1005, 1, 140.00, '', 'Customer changed mind');

-- Demo payments
INSERT INTO `payments`
(`id`, `order_id`, `payment_method`, `amount`, `payment_date`)
VALUES
(920001, 900001, 'cash', 300.00, NOW() - INTERVAL 2 HOUR),
(920002, 900002, 'gcash', 200.00, NOW() - INTERVAL 65 MINUTE);

-- Demo staff SMS logs (if cashier user exists)
INSERT INTO `staff_sms_logs`
(`id`, `staff_id`, `staff_name`, `message`, `admin_phone`, `status`, `error_message`, `sms_id`, `sent_at`, `created_at`)
SELECT 930001, @cashier_id, 'cashier', 'Need supplies: milk and coffee beans', '+639171234567', 'SENT', NULL, 'DEMO-SMS-001', NOW() - INTERVAL 50 MINUTE, NOW() - INTERVAL 50 MINUTE
WHERE @cashier_id IS NOT NULL;

INSERT INTO `staff_sms_logs`
(`id`, `staff_id`, `staff_name`, `message`, `admin_phone`, `status`, `error_message`, `sms_id`, `sent_at`, `created_at`)
SELECT 930002, @cashier_id, 'cashier', 'Espresso machine acting up', '+639171234567', 'FAILED', 'Simulated delivery failure for demo', NULL, NULL, NOW() - INTERVAL 30 MINUTE
WHERE @cashier_id IS NOT NULL;

-- Demo activity logs
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `created_at`)
SELECT 940001, @admin_id, 'add_user', 'Added demo cashier account', NOW() - INTERVAL 3 HOUR
WHERE @admin_id IS NOT NULL;

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `created_at`)
SELECT 940002, @cashier_id, 'process_payment', 'Payment processed for order #900001 via cash', NOW() - INTERVAL 2 HOUR
WHERE @cashier_id IS NOT NULL;

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `created_at`)
SELECT 940003, @cashier_id, 'update_order_item_quantity', 'Updated quantity for order #900003', NOW() - INTERVAL 20 MINUTE
WHERE @cashier_id IS NOT NULL;

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `created_at`)
SELECT 940004, @admin_id, 'adjust_stock', 'Adjusted stock for Latte: 8 to 5', NOW() - INTERVAL 10 MINUTE
WHERE @admin_id IS NOT NULL;

-- Demo inventory logs
INSERT INTO `inventory_logs`
(`id`, `menu_item_id`, `action`, `quantity_change`, `previous_stock`, `new_stock`, `order_id`, `user_id`, `notes`, `created_at`)
SELECT 950001, 1001, 'deduct', -1, 26, 25, 900001, @cashier_id, 'Stock deducted for order ORD-DEMO-0001', NOW() - INTERVAL 2 HOUR
WHERE @cashier_id IS NOT NULL;

INSERT INTO `inventory_logs`
(`id`, `menu_item_id`, `action`, `quantity_change`, `previous_stock`, `new_stock`, `order_id`, `user_id`, `notes`, `created_at`)
SELECT 950002, 1003, 'deduct', -1, 8, 7, 900001, @cashier_id, 'Stock deducted for order ORD-DEMO-0001', NOW() - INTERVAL 2 HOUR
WHERE @cashier_id IS NOT NULL;

INSERT INTO `inventory_logs`
(`id`, `menu_item_id`, `action`, `quantity_change`, `previous_stock`, `new_stock`, `order_id`, `user_id`, `notes`, `created_at`)
SELECT 950003, 1004, 'adjust', -3, 8, 5, NULL, @admin_id, 'Manual stock correction', NOW() - INTERVAL 10 MINUTE
WHERE @admin_id IS NOT NULL;

-- Demo stock alerts
INSERT INTO `stock_alerts`
(`id`, `menu_item_id`, `alert_type`, `current_stock`, `threshold`, `sent_sms`, `sent_email`, `created_at`)
VALUES
(960001, 1005, 'out_of_stock', 0, 8, 0, 0, NOW() - INTERVAL 5 MINUTE),
(960002, 1004, 'low_stock', 5, 8, 0, 0, NOW() - INTERVAL 5 MINUTE),
(960003, 1009, 'low_stock', 9, 10, 1, 0, NOW() - INTERVAL 12 MINUTE);

-- Optional: keep AUTO_INCREMENT ahead of demo ranges
ALTER TABLE `menu_items` AUTO_INCREMENT = 1011;
ALTER TABLE `orders` AUTO_INCREMENT = 900005;
ALTER TABLE `order_items` AUTO_INCREMENT = 910010;
ALTER TABLE `payments` AUTO_INCREMENT = 920003;
ALTER TABLE `staff_sms_logs` AUTO_INCREMENT = 930003;
ALTER TABLE `activity_logs` AUTO_INCREMENT = 940005;
ALTER TABLE `inventory_logs` AUTO_INCREMENT = 950004;
ALTER TABLE `stock_alerts` AUTO_INCREMENT = 960004;

-- =========================================================
-- END OF FILE
-- =========================================================
