<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = Database::getInstance();
    
    // Add is_active column to users table if it doesn't exist
    $sql = "SHOW COLUMNS FROM users LIKE 'is_active'";
    $stmt = $pdo->query($sql);
    
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER role");
        echo "Column 'is_active' added to users table successfully.\n";
    } else {
        echo "Column 'is_active' already exists in users table.\n";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
