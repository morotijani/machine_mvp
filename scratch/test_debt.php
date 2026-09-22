<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Config/Database.php';
$pdo = App\Config\Database::getInstance();

$s = $pdo->query("SELECT SUM(total_amount - paid_amount) FROM sales WHERE payment_status != 'paid' AND voided = 0")->fetchColumn() ?: 0;
$d = $pdo->query("SELECT SUM(total_amount - paid_amount) FROM standalone_debtors WHERE is_deleted = 0 AND status != 'cleared'")->fetchColumn() ?: 0;

echo "Sales Debt: $s\n";
echo "Standalone Debt: $d\n";
echo "Total Dashboard Debt: " . ($s + $d) . "\n";

// Let's also get a breakdown of sales debt by payment status
echo "\nSales Debt Breakdown:\n";
$stmt = $pdo->query("SELECT payment_status, SUM(total_amount - paid_amount) as debt FROM sales WHERE payment_status != 'paid' AND voided = 0 GROUP BY payment_status");
$breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($breakdown as $row) {
    echo " - " . $row['payment_status'] . ": " . $row['debt'] . "\n";
}

// Let's also check if there are any negative debts (overpayments) affecting the sum
echo "\nNegative Sales Debts (overpayments):\n";
$stmt = $pdo->query("SELECT id, total_amount, paid_amount FROM sales WHERE (total_amount - paid_amount) < 0 AND voided = 0");
$neg = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($neg as $row) {
    echo " - Sale ID " . $row['id'] . ": Total " . $row['total_amount'] . ", Paid " . $row['paid_amount'] . "\n";
}

?>
