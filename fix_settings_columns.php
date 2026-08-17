<?php
require_once 'C:\xampp\htdocs\machine_mvp\vendor\autoload.php';

try {
    $pdo = \App\Config\Database::getInstance();
    $driver = \App\Config\Database::getDriver();

    $newColumns = [
        'sync_master_enabled' => 'INTEGER DEFAULT 1',
        'sync_auto_enabled' => 'INTEGER DEFAULT 1',
        'sync_push_enabled' => 'INTEGER DEFAULT 1',
        'sync_pull_enabled' => 'INTEGER DEFAULT 1',
        'sync_interval_minutes' => 'INTEGER DEFAULT 5'
    ];

    if ($driver === 'sqlite') {
        $cols = $pdo->query("PRAGMA table_info(`settings`)")->fetchAll(PDO::FETCH_COLUMN, 1);
    } else {
        $cols = $pdo->query("DESCRIBE `settings`")->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    foreach ($newColumns as $col => $def) {
        if (!in_array($col, $cols)) {
            $pdo->exec("ALTER TABLE `settings` ADD COLUMN `$col` $def");
            echo "Added $col to settings\n";
        }
    }
    
    echo "Settings columns updated.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
