<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\Config\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS expenditures (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category VARCHAR(100) NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        description TEXT,
        date DATE NOT NULL,
        recorded_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
    )";

    $pdo->exec($sql);
    echo "Table 'expenditures' created successfully.\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
