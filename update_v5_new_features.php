<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Config/Database.php';

use App\Config\Database;

$pdo = Database::getInstance();

try {
    // 1. User Soft Delete column
    $pdo->exec("ALTER TABLE users ADD COLUMN is_deleted TINYINT(1) DEFAULT 0");
    echo "Added is_deleted to users table.\n";

    // 2. Note: Item Deletion usually doesn't need schema change if hard delete is allowed, 
    // but user didn't specify soft delete for items. However, if an item is deleted, 
    // it will break sales history if FKs are restricted. 
    // Standard practice for sales apps is to hide items or use soft delete.
    // User asked "Ability for admin to delete items". I will assume soft delete for items too 
    // to preserve integrity of invoices.
    $pdo->exec("ALTER TABLE items ADD COLUMN is_deleted TINYINT(1) DEFAULT 0");
    echo "Added is_deleted to items table.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
