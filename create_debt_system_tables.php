<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\Config\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = Database::getInstance();

    // 1. Standalone Debtors Table
    $sql1 = "CREATE TABLE IF NOT EXISTS standalone_debtors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        total_amount DECIMAL(15, 2) NOT NULL,
        paid_amount DECIMAL(15, 2) DEFAULT 0.00,
        description TEXT,
        status ENUM('unpaid', 'partially_paid', 'cleared') DEFAULT 'unpaid',
        is_deleted TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    // 2. Debt Repayments Table
    $sql2 = "CREATE TABLE IF NOT EXISTS debt_repayments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        debtor_id INT NOT NULL,
        amount DECIMAL(15, 2) NOT NULL,
        payment_date DATE NOT NULL,
        recorded_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (debtor_id) REFERENCES standalone_debtors(id) ON DELETE CASCADE,
        FOREIGN KEY (recorded_by) REFERENCES users(id)
    )";

    $pdo->exec($sql1);
    $pdo->exec($sql2);
    echo "Debt system tables created successfully.\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
