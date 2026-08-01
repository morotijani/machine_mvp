<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/Config/Database.php';

use App\Config\Database;

try {
    $pdo = Database::getInstance();
    
    // Create proformas table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS proformas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reference_no VARCHAR(20) NOT NULL UNIQUE,
            customer_id INT DEFAULT NULL,
            total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            recorded_by INT NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
            FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Create proforma_items table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS proforma_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            proforma_id INT NOT NULL,
            item_id INT NOT NULL,
            quantity INT NOT NULL,
            price_at_time DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (proforma_id) REFERENCES proformas(id) ON DELETE CASCADE,
            FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "Tables created successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
