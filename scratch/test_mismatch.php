<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Config/Database.php';
$pdo = App\Config\Database::getInstance();

echo "Sales mismatches (paid but owe money):\n";
$stmt = $pdo->query("SELECT id, total_amount, paid_amount FROM sales WHERE payment_status = 'paid' AND paid_amount < total_amount AND voided = 0");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "Sales mismatches (unpaid/partial but overpaid or fully paid):\n";
$stmt = $pdo->query("SELECT id, total_amount, paid_amount, payment_status FROM sales WHERE payment_status != 'paid' AND paid_amount >= total_amount AND voided = 0");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "Debtors mismatches (cleared but owe money):\n";
$stmt = $pdo->query("SELECT id, total_amount, paid_amount FROM standalone_debtors WHERE status = 'cleared' AND paid_amount < total_amount AND is_deleted = 0");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "Debtors mismatches (not cleared but fully paid):\n";
$stmt = $pdo->query("SELECT id, total_amount, paid_amount, status FROM standalone_debtors WHERE status != 'cleared' AND paid_amount >= total_amount AND is_deleted = 0");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
