<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();
$pdo = \App\Config\Database::getInstance();

echo "<h1>Fixing Local IDs for Desktop App</h1>";
try {
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
        $tablesToOffset = ['coffer_transactions', 'customer_debt_payments', 'customers', 'debt_repayments', 'expenditures', 'inventory_logs', 'item_bundles', 'item_logs', 'items', 'payment_requests', 'payments', 'proforma_items', 'proformas', 'sale_items', 'sale_return_items', 'sale_returns', 'sales', 'settings', 'standalone_debtors', 'user_logins', 'users'];
        foreach ($tablesToOffset as $tbl) {
            try {
                // Check if sequence exists
                $stmt = $pdo->query("SELECT seq FROM sqlite_sequence WHERE name = '$tbl'");
                $row = $stmt->fetch();
                if (!$row) {
                    // Doesn't exist, insert it
                    $pdo->exec("INSERT INTO sqlite_sequence (name, seq) VALUES ('$tbl', 500000)");
                    echo "<p>Inserted 500,000 sequence for table: <b>$tbl</b></p>";
                } else if ($row['seq'] < 500000) {
                    // Update it if it's less than 500,000
                    $pdo->exec("UPDATE sqlite_sequence SET seq = 500000 WHERE name = '$tbl'");
                    echo "<p>Updated sequence to 500,000 for table: <b>$tbl</b></p>";
                } else {
                    echo "<p>Table <b>$tbl</b> already has a sequence >= 500,000.</p>";
                }
            } catch (\Exception $e) {
                // Ignore errors
                echo "<p style='color:red'>Error with table $tbl: " . $e->getMessage() . "</p>";
            }
        }
        echo "<h2 style='color:green'>Done! You can delete this file now.</h2>";
    } else {
        echo "<h2 style='color:red'>This script should only be run on the Desktop (SQLite) database. You are currently connected to MySQL.</h2>";
    }
} catch (\Exception $e) {
    echo "<h2 style='color:red'>Error connecting: " . $e->getMessage() . "</h2>";
}
