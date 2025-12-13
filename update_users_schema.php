<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = Database::getInstance();
    
    // Add fullname column
    $pdo->exec("ALTER TABLE users ADD COLUMN fullname VARCHAR(100) NULL AFTER role");
    echo "Added 'fullname' column.\n";
    
    // Add profile_image column
    $pdo->exec("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL AFTER fullname");
    echo "Added 'profile_image' column.\n";
    
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
