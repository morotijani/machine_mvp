<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Config/Database.php';

$pdo = \App\Config\Database::getInstance();
try {
    $pdo->exec("ALTER TABLE settings ADD COLUMN enable_debt_module TINYINT(1) NOT NULL DEFAULT 1");
    echo "Column enable_debt_module added successfully.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
