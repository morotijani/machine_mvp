<?php
require_once __DIR__ . '/src/Config/Database.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

try {
    $pdo = Database::getInstance();
    
    // 1. Change quantity to INT(11) in sale_return_items
    $pdo->exec("ALTER TABLE sale_return_items MODIFY quantity INT(11) NOT NULL;");
    
    echo "Table sale_return_items altered successfully.\n";
} catch (Exception $e) {
    echo "Error altering table: " . $e->getMessage() . "\n";
}
