<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\Config\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = Database::getInstance();

    $sql = "ALTER TABLE expenditures ADD COLUMN is_deleted TINYINT(1) DEFAULT 0 AFTER recorded_by";

    $pdo->exec($sql);
    echo "Column 'is_deleted' added to expenditures table successfully.\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
