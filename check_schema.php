<?php
require_once __DIR__ . '/src/Config/Database.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

try {
    $pdo = Database::getInstance();
    
    // Check items table
    $stmt = $pdo->query("DESCRIBE items");
    echo "ITEMS TABLE:\n";
    while ($row = $stmt->fetch()) {
        echo print_r($row, true) . "\n";
    }

    // Check sale_items table
    $stmt = $pdo->query("DESCRIBE sale_items");
    echo "\nSALE_ITEMS TABLE:\n";
    while ($row = $stmt->fetch()) {
        echo print_r($row, true) . "\n";
    }

    // Check sale_return_items table
    $stmt = $pdo->query("DESCRIBE sale_return_items");
    echo "\nSALE_RETURN_ITEMS TABLE:\n";
    while ($row = $stmt->fetch()) {
        echo print_r($row, true) . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
