<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = Database::getInstance();

    // 1. Add type column to items
    $sql = "SHOW COLUMNS FROM items LIKE 'type'";
    $stmt = $pdo->query($sql);
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE items ADD COLUMN type ENUM('single', 'bundle') DEFAULT 'single' AFTER name");
        echo "Column 'type' added to items table.\n";
    } else {
        echo "Column 'type' already exists.\n";
    }

    // 2. Create item_bundles table
    $pdo->exec("CREATE TABLE IF NOT EXISTS item_bundles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        parent_item_id INT NOT NULL,
        child_item_id INT NOT NULL,
        quantity INT NOT NULL,
        FOREIGN KEY (parent_item_id) REFERENCES items(id) ON DELETE CASCADE,
        FOREIGN KEY (child_item_id) REFERENCES items(id) ON DELETE CASCADE
    )");
    echo "Table 'item_bundles' created/checked.\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
