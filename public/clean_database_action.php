<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Config/Database.php';

$pdo = App\Config\Database::getInstance();

echo "<html><body style='font-family: sans-serif; padding: 20px;'>";
echo "<h2>Database Cleanup Utility</h2>";

if (php_sapi_name() !== 'cli' && (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes')) {
    echo "<p style='color: red; font-weight: bold;'>WARNING: This action is destructive and irreversible. It will wipe all transaction history.</p>";
    echo "<p>Customers, Users, Items, Item Bundles, and Standalone Debtors will be preserved. Any customer with an outstanding debt will have their debt converted into a single 'Previous Balance' invoice.</p>";
    echo "<p><a href='?confirm=yes' style='background: red; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>I understand, wipe the database!</a></p>";
    echo "</body></html>";
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Calculate debts for all customers before we wipe sales
    echo "Calculating existing customer debts...\n";
    $stmt = $pdo->query("
        SELECT c.id, c.name, 
        (IFNULL(SUM(s.total_amount), 0) - IFNULL(SUM(s.paid_amount), 0)) as total_debt
        FROM customers c
        LEFT JOIN sales s ON c.id = s.customer_id AND s.voided = 0
        WHERE c.is_deleted = 0
        GROUP BY c.id
        HAVING total_debt != 0
    ");
    $customerDebts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Disable foreign key checks for the wipe
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    } else {
        $pdo->exec('PRAGMA foreign_keys = OFF');
    }

    // 3. Truncate/Delete tables we don't want
    $tablesToClean = [
        'inventory_logs',
        'item_logs',
        'sale_return_items',
        'sale_returns',
        'payments',
        'sale_items',
        'sales',
        'customer_debt_payments',
        'proforma_items',
        'proformas',
        'expenditures',
        'coffer_transactions',
        'payment_requests',
        'user_logins'
    ];

    echo "Wiping transaction tables...\n";
    foreach ($tablesToClean as $table) {
        try {
            $pdo->exec("DELETE FROM `$table`");
            if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'mysql') {
                $pdo->exec("DELETE FROM sqlite_sequence WHERE name = '$table'");
            }
        } catch (Exception $e) {
            // Ignore if table doesn't exist
            continue;
        }
    }

    // Re-enable foreign keys
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    } else {
        $pdo->exec('PRAGMA foreign_keys = ON');
    }

    // 4. Create a dummy item for 'Carried Forward Balance' if it doesn't exist
    $stmt = $pdo->prepare("SELECT id FROM items WHERE name = 'Previous Balance'");
    $stmt->execute();
    $balanceItem = $stmt->fetch();
    
    if (!$balanceItem) {
        // Find admin user to assign as creator
        $adminId = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1")->fetchColumn() ?: 1;
        
        $stmtInsert = $pdo->prepare("INSERT INTO items (name, type, price, cost_price, quantity, created_by) VALUES ('Previous Balance', 'single', 0, 0, 0, :admin_id)");
        $stmtInsert->execute(['admin_id' => $adminId]);
        $itemId = $pdo->lastInsertId();
    } else {
        $itemId = $balanceItem['id'];
    }

    // 5. Restore customer debts as new dummy sales
    echo "Restoring customer debts as starting balances...\n";
    $adminId = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1")->fetchColumn() ?: 1;

    foreach ($customerDebts as $debt) {
        $customerId = $debt['id'];
        $amount = $debt['total_debt'];

        // Create a new unpaid sale
        $stmtSale = $pdo->prepare("
            INSERT INTO sales (customer_id, user_id, total_amount, paid_amount, payment_status, invoice_number) 
            VALUES (:cid, :uid, :amount, 0, 'unpaid', :inv)
        ");
        $invNumber = 'BAL-' . str_pad($customerId, 6, '0', STR_PAD_LEFT);
        $stmtSale->execute([
            'cid' => $customerId,
            'uid' => $adminId,
            'amount' => $amount,
            'inv' => $invNumber
        ]);
        $saleId = $pdo->lastInsertId();

        // Create the sale item
        $stmtItem = $pdo->prepare("
            INSERT INTO sale_items (sale_id, item_id, quantity, price_at_sale, subtotal)
            VALUES (:sid, :iid, 1, :price, :sub)
        ");
        $stmtItem->execute([
            'sid' => $saleId,
            'iid' => $itemId,
            'price' => $amount,
            'sub' => $amount
        ]);
    }

    $pdo->commit();
    echo "<div style='color: green; border: 1px solid green; padding: 15px; background: #e6f4ea;'>";
    echo "<h3>Database successfully cleaned!</h3>";
    echo "<p>All transaction history is wiped.</p>";
    echo "<p>Customer debts have been preserved as new 'Previous Balance' invoices.</p>";
    echo "</div>";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<div style='color: red;'>Error during cleanup: " . htmlspecialchars($e->getMessage()) . "</div>";
}
echo "<p><a href='/'>Go back to Dashboard</a></p>";
echo "</body></html>";
