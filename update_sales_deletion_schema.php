<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = Database::getInstance();

    // 1. Ensure voided column exists FIRST
    $sql = "SHOW COLUMNS FROM sales LIKE 'voided'";
    $stmt = $pdo->query($sql);
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE sales ADD COLUMN voided TINYINT(1) DEFAULT 0 AFTER payment_status");
        echo "Column 'voided' added to sales table.\n";
    } else {
        echo "Column 'voided' already exists.\n";
    }
    
    // 2. Add delete_request_status column (Now safe to place AFTER voided)
    $sql = "SHOW COLUMNS FROM sales LIKE 'delete_request_status'";
    $stmt = $pdo->query($sql);
    
    if ($stmt->rowCount() == 0) {
        // ENUM: none, pending, approved, rejected
        $pdo->exec("ALTER TABLE sales ADD COLUMN delete_request_status ENUM('none', 'pending', 'approved', 'rejected') DEFAULT 'none' AFTER voided");
        echo "Column 'delete_request_status' added to sales table.\n";
    } else {
        echo "Column 'delete_request_status' already exists.\n";
    }

    // 3. Add delete_requested_at column
    $sql = "SHOW COLUMNS FROM sales LIKE 'delete_requested_at'";
    $stmt = $pdo->query($sql);
    
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE sales ADD COLUMN delete_requested_at DATETIME NULL AFTER delete_request_status");
        echo "Column 'delete_requested_at' added to sales table.\n";
    } else {
        echo "Column 'delete_requested_at' already exists.\n";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
