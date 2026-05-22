<?php
require 'vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
require 'src/Config/Database.php';

$pdo = \App\Config\Database::getInstance();
$today = date('Y-m-d');
echo "Today: $today\n";

// Get Wasty's ID
$stmt = $pdo->query("SELECT id FROM users WHERE username = 'Wasty'");
$wastyId = $stmt->fetchColumn();
echo "Wasty ID: $wastyId\n";

function getStats($pdo, $today, $uid = null) {
    $isLimitedView = ($uid !== null);
    $params = $isLimitedView ? ['uid' => $uid] : [];
    
    // Total Payments
    $sqlPayToday = "SELECT SUM(p.amount) FROM payments p JOIN sales s ON p.sale_id = s.id WHERE DATE(p.payment_date) = :today" . ($isLimitedView ? " AND p.recorded_by = :uid" : "");
    $stmt = $pdo->prepare($sqlPayToday);
    $stmt->execute(array_merge(['today' => $today], $params));
    $totalPaymentsToday = $stmt->fetchColumn() ?: 0;

    // Debt Recovered
    $sqlDebtCol = "SELECT SUM(p.amount) FROM payments p JOIN sales s ON p.sale_id = s.id WHERE DATE(p.payment_date) = :today_pay AND DATE(s.created_at) < :today_sale" . ($isLimitedView ? " AND p.recorded_by = :uid" : "");
    $stmt = $pdo->prepare($sqlDebtCol);
    $stmt->execute(array_merge(['today_pay' => $today, 'today_sale' => $today], $params));
    $todayDebtCollected = $stmt->fetchColumn() ?: 0;

    // Gross New Sales
    $sqlNewPay = "SELECT SUM(p.amount) FROM payments p JOIN sales s ON p.sale_id = s.id WHERE DATE(p.payment_date) = :today_pay AND DATE(s.created_at) = :today_sale" . ($isLimitedView ? " AND p.recorded_by = :uid" : "");
    $stmt = $pdo->prepare($sqlNewPay);
    $stmt->execute(array_merge(['today_pay' => $today, 'today_sale' => $today], $params));
    $todayNewSalesGross = $stmt->fetchColumn() ?: 0;

    // Returns
    $sqlNewReturns = "SELECT SUM(r.cash_refunded) FROM sale_returns r JOIN sales s ON r.sale_id = s.id WHERE DATE(r.created_at) = :today_ret AND DATE(s.created_at) = :today_sale" . ($isLimitedView ? " AND r.recorded_by = :uid" : "");
    $stmt = $pdo->prepare($sqlNewReturns);
    $stmt->execute(array_merge(['today_ret' => $today, 'today_sale' => $today], $params));
    $todayReturnsValueNew = $stmt->fetchColumn() ?: 0;

    $sqlTotalReturns = "SELECT SUM(cash_refunded) FROM sale_returns WHERE DATE(created_at) = :today_return" . ($isLimitedView ? " AND recorded_by = :uid" : "");
    $stmt = $pdo->prepare($sqlTotalReturns);
    $stmt->execute(array_merge(['today_return' => $today], $params));
    $totalReturnsToday = $stmt->fetchColumn() ?: 0;

    // Voids
    $sqlVoidedToday = "SELECT SUM(p.amount) FROM payments p JOIN sales s ON p.sale_id = s.id WHERE s.voided = 1 AND DATE(s.voided_at) = :today" . ($isLimitedView ? " AND p.recorded_by = :uid" : "");
    $stmt = $pdo->prepare($sqlVoidedToday);
    $stmt->execute(array_merge(['today' => $today], $params));
    $voidedRefundsToday = $stmt->fetchColumn() ?: 0;

    // Results
    $todayNewSalesCollected = $todayNewSalesGross - $todayReturnsValueNew;
    $totalNetCollections = $totalPaymentsToday - $totalReturnsToday - $voidedRefundsToday;

    return [
        'Total Payments (Gross)' => $totalPaymentsToday,
        'Gross New Sales' => $todayNewSalesGross,
        'Returns (New)' => $todayReturnsValueNew,
        'Cash Collected Today (Net New)' => $todayNewSalesCollected,
        'Total Returns' => $totalReturnsToday,
        'Voided Refunds' => $voidedRefundsToday,
        'Total Net Collections' => $totalNetCollections
    ];
}

echo "\n--- ADMIN ---\n";
print_r(getStats($pdo, $today));

echo "\n--- WASTY ---\n";
print_r(getStats($pdo, $today, $wastyId));
