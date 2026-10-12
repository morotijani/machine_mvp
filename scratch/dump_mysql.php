<?php
$pdo = new PDO('mysql:host=localhost;dbname=machine_mvp_online;charset=utf8mb4', 'root', '');
$tables = ['coffer_transactions', 'customer_debt_payments', 'customers', 'debt_repayments', 'expenditures', 'inventory_logs', 'item_bundles', 'item_logs', 'items', 'payment_requests', 'payments', 'sale_return_items', 'sale_returns', 'sales', 'settings', 'standalone_debtors', 'user_logins', 'users'];
foreach ($tables as $t) {
    $stmt = $pdo->query("SHOW CREATE TABLE `$t`");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row['Create Table'] . ";\n\n";
}
