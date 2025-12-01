-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 01:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `employee_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `created_at`) VALUES
(1, 2, 'process_payment', 'Payment processed for order #1 via cash', '2025-11-13 13:32:05'),
(2, 2, 'process_payment', 'Payment processed for order #2 via cash', '2025-11-13 13:32:36'),
(3, 1, 'send_daily_report', 'Sent daily sales report to alameraamethys@gmail.com', '2025-11-13 14:21:20'),
(4, 1, 'send_daily_report', 'Sent daily sales report to alameraamethyst@gmail.com', '2025-11-13 14:24:01'),
(5, 2, 'sms_sent', 'Sent SMS to admin: Customer complaint: [details]', '2025-11-20 09:42:24'),
(6, 2, 'sms_sent', 'Sent SMS to admin: Need supplies admin', '2025-11-20 09:52:04'),
(7, 2, 'sms_sent', 'Sent SMS to admin: Need supplies: [item name]', '2025-11-20 09:56:14'),
(8, 2, 'sms_sent', 'Sent SMS to admin: Urgent: [your message]', '2025-11-20 09:58:41'),
(9, 1, 'send_daily_report', 'Sent daily sales report to alameraamethys@gmail.com', '2025-11-21 02:30:06'),
(10, 2, 'sms_sent', 'Sent SMS to admin: Need supplies: [item name]', '2025-11-21 02:31:16'),
(11, 1, 'send_daily_report', 'Sent daily sales report to amaneslera1@gmail.com', '2025-11-21 02:31:57');

-- --------------------------------------------------------

--
-- Table structure for table `barcode`
--

CREATE TABLE `barcode` (
  `id` int(11) NOT NULL,
  `number` varchar(120) NOT NULL,
  `name` varchar(150) NOT NULL,
  `batchnumber` varchar(40) NOT NULL,
  `prodesc` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barcode`
--

INSERT INTO `barcode` (`id`, `number`, `name`, `batchnumber`, `prodesc`) VALUES
(1, '101010', 'Tvs electrons', 'B1', 'Electron device'),
(2, '1010', 'test', 'B2', 'TESTING PRODUCT'),
(3, '111111111', 'Xiaomi', 'B1231', 'Cellphone');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `category`, `description`, `price`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Espresso', 'Coffee', 'Strong and bold espresso shot', 80.00, 'espresso.jpg', 'available', '2025-10-30 13:35:50', '2025-10-30 13:35:50'),
(2, 'Americano', 'Coffee', 'Espresso with hot water', 95.00, 'americano.jpg', 'available', '2025-10-30 13:35:50', '2025-10-30 13:35:50'),
(3, 'Cappuccino', 'Coffee', 'Espresso with steamed milk and foam', 120.00, 'cappuccino.jpg', 'available', '2025-10-30 13:35:50', '2025-10-30 13:35:50'),
(4, 'Latte', 'Coffee', 'Espresso with steamed milk', 130.00, 'latte.jpg', 'available', '2025-10-30 13:35:50', '2025-10-30 13:35:50'),
(5, 'Mocha', 'Coffee', 'Chocolate-flavored latte', 140.00, 'mocha.jpg', 'available', '2025-10-30 13:35:50', '2025-10-30 13:35:50'),
(6, 'Caramel Macchiato', 'Coffee', 'Vanilla latte with caramel drizzle', 150.00, 'caramel_macchiato.jpg', 'available', '2025-10-30 13:35:50', '2025-10-30 13:35:50'),
(7, 'Chocolate Chip Cookie', 'Snacks', 'Freshly baked chocolate chip cookie', 45.00, 'cookie.jpg', 'available', '2025-10-30 13:35:50', '2025-10-30 13:35:50'),
(8, 'Blueberry Muffin', 'Snacks', 'Soft and fluffy blueberry muffin', 65.00, 'muffin.jpg', 'available', '2025-10-30 13:35:50', '2025-10-30 13:35:50'),
(9, 'Croissant', 'Snacks', 'Buttery and flaky croissant', 75.00, 'croissant.jpg', 'available', '2025-10-30 13:35:50', '2025-10-30 13:35:50'),
(10, 'Ham & Cheese Sandwich', 'Snacks', 'Toasted sandwich with ham and cheese', 95.00, 'sandwich.jpg', 'available', '2025-10-30 13:35:50', '2025-10-30 13:35:50');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `msg_id` int(11) NOT NULL,
  `incoming_msg_id` int(255) NOT NULL,
  `outgoing_msg_id` int(255) NOT NULL,
  `msg` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`msg_id`, `incoming_msg_id`, `outgoing_msg_id`, `msg`) VALUES
(0, 2, 1, 'hahsha'),
(0, 1, 2, 'hello'),
(0, 1, 2, 'hel'),
(0, 1, 2, 'he'),
(0, 1, 2, 'khe'),
(0, 2, 1, 'hello'),
(0, 1, 2, 'hi '),
(0, 1, 2, 'HAHHA '),
(0, 1, 2, 'adawda'),
(0, 1, 2, 'hello admin'),
(0, 2, 1, 'hello admin'),
(0, 1, 2, 'hello cashier'),
(0, 2, 1, 'hi'),
(0, 1, 2, 'dwow'),
(0, 2, 1, 'who you'),
(0, 1, 2, 'trabahante ko nimo sir'),
(0, 1, 2, 'kawatan na nako imong business'),
(0, 2, 1, 'ikaw bahala'),
(0, 2, 1, 'basta binli lng ko'),
(0, 1, 2, 'gegege noted');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2024-01-01-000002', 'App\\Database\\Migrations\\CreateMenuItemsTable', 'default', 'App', 1761831280, 1),
(2, '2024-01-01-000003', 'App\\Database\\Migrations\\CreateOrdersTable', 'default', 'App', 1761831281, 1),
(3, '2024-01-01-000004', 'App\\Database\\Migrations\\CreateOrderItemsTable', 'default', 'App', 1761831281, 1),
(4, '2024-01-01-000005', 'App\\Database\\Migrations\\CreatePaymentsTable', 'default', 'App', 1761831281, 1),
(5, '2024-01-01-000006', 'App\\Database\\Migrations\\CreateActivityLogsTable', 'default', 'App', 1763627329, 1),
(6, '2025-11-20-100000', 'App\\Database\\Migrations\\CreateStaffSmsLogsTable', 'default', 'App', 1763627345, 2);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) UNSIGNED NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `status` enum('pending','paid','completed','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `status`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 'ORD2025103058CC98', 'paid', 295.00, '2025-10-30 14:21:26', '2025-11-13 13:32:05'),
(2, 'ORD20251113484C33', 'paid', 295.00, '2025-11-13 13:05:08', '2025-11-13 13:32:36');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `menu_item_id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `addons` text DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`, `addons`, `notes`) VALUES
(1, 1, 2, 1, 95.00, '', ''),
(2, 1, 1, 1, 80.00, '', ''),
(3, 1, 3, 1, 120.00, '', ''),
(4, 2, 1, 1, 80.00, '', ''),
(5, 2, 2, 1, 95.00, '', ''),
(6, 2, 3, 1, 120.00, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `amount`, `payment_date`) VALUES
(1, 1, 'cash', 295.00, '2025-11-13 13:32:05'),
(2, 2, 'cash', 295.00, '2025-11-13 13:32:36');

-- --------------------------------------------------------

--
-- Table structure for table `staff_sms_logs`
--

CREATE TABLE `staff_sms_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `staff_id` int(11) UNSIGNED NOT NULL,
  `staff_name` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `admin_phone` varchar(20) NOT NULL,
  `status` enum('SENT','FAILED') NOT NULL DEFAULT 'FAILED',
  `error_message` text DEFAULT NULL,
  `sms_id` varchar(100) DEFAULT NULL COMMENT 'SMS API message ID',
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_sms_logs`
--

INSERT INTO `staff_sms_logs` (`id`, `staff_id`, `staff_name`, `message`, `admin_phone`, `status`, `error_message`, `sms_id`, `sent_at`, `created_at`) VALUES
(1, 2, 'cashier', 'Machine not working:hey', '+639686186310', 'FAILED', 'Failed to send SMS. Please try again later.', NULL, NULL, '2025-11-20 08:36:23'),
(2, 2, 'cashier', 'Machine not working:hey', '+639169412943', 'FAILED', 'Failed to send SMS. Please try again later.', NULL, NULL, '2025-11-20 08:37:44'),
(3, 2, 'cashier', 'Machine not working:hey', '+639169412943', 'FAILED', 'Failed to send SMS. Please try again later.', NULL, NULL, '2025-11-20 08:39:58'),
(4, 2, 'cashier', 'Need supplies: [item name]', '+639686186310', 'FAILED', 'Failed to send SMS. The number +63968618XXXX is unverified. Trial accounts cannot send messages to unverified numbers; verify +63968618XXXX at twilio.com/user/account/phone-numbers/verified, or purchase a Twilio number to send messages to unverified numbers', NULL, NULL, '2025-11-20 09:15:09'),
(5, 2, 'cashier', 'Customer complaint: [details]', '+639686186310', 'SENT', NULL, 'iSms-WxtlP9', '2025-11-20 01:42:24', '2025-11-20 09:42:24'),
(6, 2, 'cashier', 'Need supplies admin', '+639686186310', 'SENT', NULL, 'iSms-LNo4pH', '2025-11-20 01:52:04', '2025-11-20 09:52:04'),
(7, 2, 'cashier', 'Need supplies: [item name]', '639686186310', 'SENT', NULL, 'iSms-2EwNB2', '2025-11-20 01:56:14', '2025-11-20 09:56:14'),
(8, 2, 'cashier', 'Urgent: [your message]', '09686186310', 'SENT', NULL, 'iSms-GSac89', '2025-11-20 01:58:40', '2025-11-20 09:58:40'),
(9, 2, 'cashier', 'Need supplies: [item name]', '09686186310', 'SENT', NULL, 'iSms-Uzx3xs', '2025-11-20 18:31:16', '2025-11-21 02:31:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','cashier') NOT NULL DEFAULT 'cashier',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$2UNLkGlx657szgsVwdumQOkLMThkgg1mCkf/L58a6WhRpXXuwjpcO', 'Admin', '2025-09-11 10:36:13', '2025-09-11 10:36:13'),
(2, 'cashier', '$2y$10$2xiRipxo3tf3DEajV6oYAOSu0nM25g7zYZYFzrpSHt2VwQcUgBTzm', 'cashier', '2025-09-11 10:36:13', '2025-09-11 10:36:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `barcode`
--
ALTER TABLE `barcode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_menu_item_id_foreign` (`menu_item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_foreign` (`order_id`);

--
-- Indexes for table `staff_sms_logs`
--
ALTER TABLE `staff_sms_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `status` (`status`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `barcode`
--
ALTER TABLE `barcode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff_sms_logs`
--
ALTER TABLE `staff_sms_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `staff_sms_logs`
--
ALTER TABLE `staff_sms_logs`
  ADD CONSTRAINT `staff_sms_logs_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
