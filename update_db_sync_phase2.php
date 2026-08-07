<?php
require 'vendor/autoload.php';

$pdo = \App\Config\Database::getInstance();

try {
    $tablesToUpdate = [
        'user_logins',
        'sale_return_items',
        'sale_returns',
        'payment_requests',
        'item_logs',
        'item_bundles',
        'debt_repayments',
        'customer_debt_payments',
        'coffer_transactions'
    ];

    foreach ($tablesToUpdate as $table) {
        // Check if table exists first
        $tableExists = $pdo->query("SHOW TABLES LIKE '$table'")->rowCount() > 0;
        
        if ($tableExists) {
            echo "Processing table: $table\n";
            
            // Add sync_status
            try {
                $pdo->exec("ALTER TABLE `$table` ADD COLUMN sync_status TINYINT DEFAULT 0");
                echo " - Added sync_status column.\n";
            } catch (\PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                    echo " - sync_status column already exists.\n";
                } else {
                    echo " - Error adding sync_status: " . $e->getMessage() . "\n";
                }
            }

            // Add updated_at
            try {
                $pdo->exec("ALTER TABLE `$table` ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
                echo " - Added updated_at column.\n";
            } catch (\PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                    echo " - updated_at column already exists.\n";
                } else {
                    echo " - Error adding updated_at: " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "Skipped: Table '$table' does not exist in this database.\n";
        }
    }
    
    echo "\nDatabase sync preparation (Phase 2) complete.\n";
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
