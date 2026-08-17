<?php
require_once 'C:\xampp\htdocs\machine_mvp\vendor\autoload.php';

try {
    $pdo = \App\Config\Database::getInstance();
    $driver = \App\Config\Database::getDriver();

    $tablesToUpdate = ['proformas', 'proforma_items'];

    foreach ($tablesToUpdate as $table) {
        if ($driver === 'sqlite') {
            $cols = $pdo->query("PRAGMA table_info(`$table`)")->fetchAll(PDO::FETCH_COLUMN, 1);
        } else {
            $cols = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_COLUMN, 0);
        }
        
        if (!in_array('sync_status', $cols)) {
            $pdo->exec("ALTER TABLE `$table` ADD COLUMN `sync_status` INTEGER DEFAULT 0");
            echo "Added sync_status to $table\n";
        }
        
        if (!in_array('updated_at', $cols)) {
            $pdo->exec("ALTER TABLE `$table` ADD COLUMN `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP");
            echo "Added updated_at to $table\n";
        }
        
        if (!in_array('uuid', $cols)) {
            $pdo->exec("ALTER TABLE `$table` ADD COLUMN `uuid` VARCHAR(36) NULL");
            echo "Added uuid to $table\n";
        }
    }
    
    $extraCols = [
        'proformas' => ['customer_uuid', 'user_uuid'],
        'proforma_items' => ['proforma_uuid', 'item_uuid']
    ];
    
    foreach ($extraCols as $table => $columns) {
        if ($driver === 'sqlite') {
            $cols = $pdo->query("PRAGMA table_info(`$table`)")->fetchAll(PDO::FETCH_COLUMN, 1);
        } else {
            $cols = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_COLUMN, 0);
        }
        foreach ($columns as $c) {
            if (!in_array($c, $cols)) {
                $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$c` VARCHAR(36) NULL");
                echo "Added $c to $table\n";
            }
        }
    }
    
    echo "Done.\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
