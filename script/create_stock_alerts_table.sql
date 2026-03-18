-- Create stock_alerts table for US04
CREATE TABLE IF NOT EXISTS `stock_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_item_id` int(11) unsigned NOT NULL,
  `alert_type` enum('low_stock','out_of_stock') DEFAULT 'low_stock',
  `current_stock` int(11) unsigned NOT NULL,
  `threshold` int(11) unsigned NOT NULL,
  `sent_sms` tinyint(1) DEFAULT 0,
  `sent_email` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_item_id` (`menu_item_id`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_alerts_type_date ON stock_alerts(alert_type, created_at);
CREATE INDEX idx_alerts_sms_status ON stock_alerts(sent_sms, created_at);
