<?php
namespace App\Config;

use PDO;

class Migration
{
  public static function run(PDO $pdo)
  {
    $schema = <<<SQL
CREATE TABLE IF NOT EXISTS `coffer_transactions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `amount` REAL NOT NULL,
  `type` TEXT DEFAULT 'withdrawal',
  `purpose` text NOT NULL,
  `recorded_by` INTEGER NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
);

CREATE TABLE IF NOT EXISTS `customer_debt_payments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `customer_id` INTEGER NOT NULL,
  `amount` REAL NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recorded_by` INTEGER DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
);

CREATE TABLE IF NOT EXISTS `customers` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL,
  `phone` TEXT DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` INTEGER DEFAULT 0,
  `created_by` INTEGER DEFAULT NULL,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS `debt_repayments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `debtor_id` INTEGER NOT NULL,
  `amount` REAL NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `payment_date` date NOT NULL,
  `recorded_by` INTEGER NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`debtor_id`) REFERENCES `standalone_debtors` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
);

CREATE TABLE IF NOT EXISTS `expenditures` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `category` TEXT NOT NULL,
  `amount` REAL NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `recorded_by` INTEGER DEFAULT NULL,
  `is_deleted` INTEGER DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_status` INTEGER DEFAULT 0,
  FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS `inventory_logs` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `item_id` INTEGER NOT NULL,
  `change_amount` INTEGER NOT NULL,
  `reason` TEXT DEFAULT NULL,
  `user_id` INTEGER DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `item_bundles` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `parent_item_id` INTEGER NOT NULL,
  `child_item_id` INTEGER NOT NULL,
  `quantity` INTEGER NOT NULL,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`parent_item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`child_item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `item_logs` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `item_id` INTEGER NOT NULL,
  `user_id` INTEGER NOT NULL,
  `action` TEXT NOT NULL,
  `details` text DEFAULT NULL,
  `old_quantity` INTEGER DEFAULT NULL,
  `new_quantity` INTEGER DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `items` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL,
  `type` TEXT DEFAULT 'single',
  `category` TEXT DEFAULT NULL,
  `sku` TEXT DEFAULT NULL,
  `unit` TEXT DEFAULT 'pcs',
  `price` REAL NOT NULL,
  `cost_price` REAL NOT NULL,
  `quantity` INTEGER NOT NULL DEFAULT 0,
  `location` TEXT DEFAULT NULL,
  `image_path` TEXT DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` INTEGER DEFAULT 0,
  `created_by` INTEGER DEFAULT NULL,
  `sync_status` INTEGER DEFAULT 0,
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS `payment_requests` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `type` TEXT NOT NULL,
  `reference_id` INTEGER NOT NULL,
  `customer_id` INTEGER DEFAULT NULL,
  `amount_due` REAL NOT NULL,
  `status` TEXT DEFAULT 'pending',
  `created_by` INTEGER NOT NULL,
  `cashier_id` INTEGER DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_at` timestamp NULL DEFAULT NULL,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS `payments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `sale_id` INTEGER NOT NULL,
  `amount` REAL NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recorded_by` INTEGER DEFAULT NULL,
  `customer_debt_payment_id` INTEGER DEFAULT NULL,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`customer_debt_payment_id`) REFERENCES `customer_debt_payments` (`id`),
  FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
);

CREATE TABLE IF NOT EXISTS `proforma_items` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `proforma_id` INTEGER NOT NULL,
  `item_id` INTEGER NOT NULL,
  `quantity` INTEGER NOT NULL,
  `price_at_time` REAL NOT NULL,
  FOREIGN KEY (`proforma_id`) REFERENCES `proformas` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`item_id`) REFERENCES `items` (`id`)
);

CREATE TABLE IF NOT EXISTS `proformas` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `reference_no` TEXT NOT NULL,
  `customer_id` INTEGER DEFAULT NULL,
  `total_amount` REAL NOT NULL DEFAULT 0.00,
  `recorded_by` INTEGER NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
);

CREATE TABLE IF NOT EXISTS `sale_items` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `sale_id` INTEGER NOT NULL,
  `item_id` INTEGER DEFAULT NULL,
  `quantity` INTEGER NOT NULL,
  `price_at_sale` REAL NOT NULL,
  `subtotal` REAL NOT NULL,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS `sale_return_items` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `return_id` INTEGER NOT NULL,
  `item_id` INTEGER NOT NULL,
  `quantity` INTEGER NOT NULL,
  `price_at_sale` REAL NOT NULL,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`item_id`) REFERENCES `items` (`id`)
);

CREATE TABLE IF NOT EXISTS `sale_returns` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `sale_id` INTEGER NOT NULL,
  `total_deduction` REAL NOT NULL,
  `cash_refunded` REAL NOT NULL DEFAULT 0.00,
  `recorded_by` INTEGER NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`)
);

CREATE TABLE IF NOT EXISTS `sales` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `customer_id` INTEGER DEFAULT NULL,
  `user_id` INTEGER NOT NULL,
  `total_amount` REAL NOT NULL,
  `paid_amount` REAL NOT NULL DEFAULT 0.00,
  `payment_status` TEXT DEFAULT 'unpaid',
  `voided` INTEGER DEFAULT 0,
  `delete_request_status` TEXT DEFAULT 'none',
  `delete_requested_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `voided_at` timestamp NULL DEFAULT NULL,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `company_name` TEXT NOT NULL DEFAULT 'POS LITE',
  `company_address` text DEFAULT NULL,
  `company_phone` TEXT DEFAULT NULL,
  `company_email` TEXT DEFAULT NULL,
  `company_logo` TEXT DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `receipt_type` TEXT DEFAULT 'a4',
  `enable_debt_module` INTEGER NOT NULL DEFAULT 1,
  `cloud_url` TEXT DEFAULT NULL,
  `sync_api_key` TEXT DEFAULT NULL,
  `sync_status` INTEGER DEFAULT 0,
  `enable_barcode_reader` INTEGER DEFAULT 1,
  `enable_desktop_setup` INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS `standalone_debtors` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL,
  `phone` TEXT DEFAULT NULL,
  `total_amount` REAL NOT NULL,
  `paid_amount` REAL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `status` TEXT DEFAULT 'unpaid',
  `is_deleted` INTEGER DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_status` INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS `user_logins` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER NOT NULL,
  `login_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` TEXT DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `users` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `username` TEXT NOT NULL,
  `password` TEXT NOT NULL,
  `role` TEXT NOT NULL DEFAULT 'sales',
  `is_active` INTEGER DEFAULT 1,
  `fullname` TEXT DEFAULT NULL,
  `profile_image` TEXT DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` INTEGER DEFAULT 0,
  `sync_status` INTEGER DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
);
SQL;

    // Split by semi-colon and execute to avoid PDO sqlite batch execute issues on some versions
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    foreach ($statements as $stmt) {
      if (!empty($stmt)) {
        $pdo->exec($stmt);
      }
    }

    // ALTER EXISTING TABLES IF NECESSARY
    try {
        $pdo->exec("ALTER TABLE items ADD COLUMN created_by INTEGER DEFAULT NULL");
    } catch (\Exception $e) {
        // Column already exists or error
    }

    // Seed default admin if missing
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    if ($count == 0) {
      $defaultHash = password_hash('admin123', PASSWORD_DEFAULT);
      $pdo->exec("INSERT INTO users (username, password, role, fullname) VALUES ('admin', '$defaultHash', 'admin', 'System Administrator')");
    }

    // Offset Auto-Increment IDs to prevent collisions with Cloud Database (which starts at 1)
    // Only applies to SQLite, ensuring the local desktop generates IDs > 500,000
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
        $tablesToOffset = ['coffer_transactions', 'customer_debt_payments', 'customers', 'debt_repayments', 'expenditures', 'inventory_logs', 'item_bundles', 'item_logs', 'items', 'payment_requests', 'payments', 'proforma_items', 'proformas', 'sale_items', 'sale_return_items', 'sale_returns', 'sales', 'settings', 'standalone_debtors', 'user_logins', 'users'];
        foreach ($tablesToOffset as $tbl) {
            try {
                // Check if sequence exists
                $stmt = $pdo->query("SELECT seq FROM sqlite_sequence WHERE name = '$tbl'");
                if (!$stmt->fetch()) {
                    // Doesn't exist, insert it
                    $pdo->exec("INSERT INTO sqlite_sequence (name, seq) VALUES ('$tbl', 500000)");
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }
    }
  }
}
