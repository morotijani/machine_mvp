<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\Config\Database;

$pdo = Database::getInstance();
$stmt = $pdo->query('DESCRIBE payments');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
