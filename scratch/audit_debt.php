<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Config/Database.php';
$pdo = App\Config\Database::getInstance();

echo "--- TOTAL DASHBOARD CALCULATION ---\n";
$s = $pdo->query("SELECT SUM(total_amount - paid_amount) FROM sales WHERE payment_status != 'paid' AND voided = 0")->fetchColumn() ?: 0;
$d = $pdo->query("SELECT SUM(total_amount - paid_amount) FROM standalone_debtors WHERE is_deleted = 0 AND status != 'cleared'")->fetchColumn() ?: 0;
echo "Total Sales Debt: GHS " . number_format($s, 2) . "\n";
echo "Total Standalone Debt: GHS " . number_format($d, 2) . "\n";
echo "Total Dashboard Debt: GHS " . number_format($s + $d, 2) . "\n\n";

echo "--- DETAILED SALES DEBT BREAKDOWN ---\n";
$sales = $pdo->query("SELECT id, invoice_number, total_amount, paid_amount, (total_amount - paid_amount) as debt, payment_status FROM sales WHERE payment_status != 'paid' AND voided = 0 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

$calculated_sales_debt = 0;
foreach($sales as $sale) {
    echo "Sale #" . $sale['id'] . " (Invoice: " . $sale['invoice_number'] . ") - Status: " . $sale['payment_status'] . " - Debt: GHS " . number_format($sale['debt'], 2) . "\n";
    $calculated_sales_debt += $sale['debt'];
}
echo "Sum of all individual sales debts: GHS " . number_format($calculated_sales_debt, 2) . "\n\n";

echo "--- DETAILED STANDALONE DEBT BREAKDOWN ---\n";
$debtors = $pdo->query("SELECT id, name, total_amount, paid_amount, (total_amount - paid_amount) as debt, status FROM standalone_debtors WHERE is_deleted = 0 AND status != 'cleared' ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

$calculated_standalone_debt = 0;
foreach($debtors as $debtor) {
    echo "Debtor #" . $debtor['id'] . " (" . $debtor['name'] . ") - Status: " . $debtor['status'] . " - Debt: GHS " . number_format($debtor['debt'], 2) . "\n";
    $calculated_standalone_debt += $debtor['debt'];
}
echo "Sum of all individual standalone debts: GHS " . number_format($calculated_standalone_debt, 2) . "\n";
