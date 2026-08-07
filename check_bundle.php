<?php
require 'vendor/autoload.php';
$pdo = \App\Config\Database::getInstance();
$stmt = $pdo->query('SHOW CREATE TABLE item_bundles');
print_r($stmt->fetch());
