<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\Config\Database;

try {
    $pdo = Database::getInstance();
    
    // 1. Create table (DDL usually commits implicitly in MySQL/MariaDB, so we do it outside transaction if needed,
    // but here we'll try to keep it safe)
    $pdo->exec("CREATE TABLE IF NOT EXISTS customer_debt_payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        recorded_by INT,
        notes TEXT,
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (recorded_by) REFERENCES users(id)
    )");

    $pdo->beginTransaction();

    // 2. Add column and FK
    $pdo->exec("ALTER TABLE payments ADD COLUMN customer_debt_payment_id INT NULL AFTER recorded_by");
    $pdo->exec("ALTER TABLE payments ADD CONSTRAINT fk_debt_payment FOREIGN KEY (customer_debt_payment_id) REFERENCES customer_debt_payments(id)");

    $pdo->commit();
    echo "Migration successful\n";
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    echo "Migration failed: " . $e->getMessage() . "\n";
}
