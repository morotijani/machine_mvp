<?php
require 'vendor/autoload.php';

$pdo = \App\Config\Database::getInstance();

try {
    $commands = [
        "ALTER TABLE settings ADD COLUMN cloud_url VARCHAR(255) NULL",
        "ALTER TABLE settings ADD COLUMN sync_api_key VARCHAR(255) NULL",
        "ALTER TABLE settings ADD COLUMN sync_status TINYINT DEFAULT 0",
        
        "ALTER TABLE users ADD COLUMN sync_status TINYINT DEFAULT 0",
        "ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        
        "ALTER TABLE customers ADD COLUMN sync_status TINYINT DEFAULT 0",
        "ALTER TABLE customers ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        
        "ALTER TABLE items ADD COLUMN sync_status TINYINT DEFAULT 0",
        
        "ALTER TABLE sales ADD COLUMN sync_status TINYINT DEFAULT 0",
        "ALTER TABLE sales ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        
        "ALTER TABLE sale_items ADD COLUMN sync_status TINYINT DEFAULT 0",
        "ALTER TABLE sale_items ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        
        "ALTER TABLE payments ADD COLUMN sync_status TINYINT DEFAULT 0",
        "ALTER TABLE payments ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        
        "ALTER TABLE inventory_logs ADD COLUMN sync_status TINYINT DEFAULT 0",
        "ALTER TABLE inventory_logs ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];

    foreach ($commands as $cmd) {
        try {
            $pdo->exec($cmd);
            echo "Executed: $cmd\n";
        } catch (\PDOException $e) {
            echo "Skipped/Error on ($cmd): " . $e->getMessage() . "\n";
        }
    }
    
    // Check if debtors table exists, then alter it
    $debtorsExist = $pdo->query("SHOW TABLES LIKE 'debtors'")->rowCount() > 0;
    if ($debtorsExist) {
        try {
            $pdo->exec("ALTER TABLE debtors ADD COLUMN sync_status TINYINT DEFAULT 0");
            $pdo->exec("ALTER TABLE debtors ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
            echo "Altered debtors table.\n";
        } catch (\PDOException $e) {}
    }
    
    // Check if expenditures table exists, then alter it
    $expendituresExist = $pdo->query("SHOW TABLES LIKE 'expenditures'")->rowCount() > 0;
    if ($expendituresExist) {
        try {
            $pdo->exec("ALTER TABLE expenditures ADD COLUMN sync_status TINYINT DEFAULT 0");
            $pdo->exec("ALTER TABLE expenditures ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
            echo "Altered expenditures table.\n";
        } catch (\PDOException $e) {}
    }
    
    echo "Database sync preparation complete.\n";
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
