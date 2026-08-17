<?php
require 'vendor/autoload.php';
try {
    $pdo = App\Config\Database::getInstance();
    $p = $pdo->query('SELECT COUNT(*) FROM proformas WHERE uuid IS NULL OR uuid = ""')->fetchColumn();
    $pi = $pdo->query('SELECT COUNT(*) FROM proforma_items WHERE uuid IS NULL OR uuid = ""')->fetchColumn();
    echo "proformas with null/empty uuid: $p\n";
    echo "proforma_items with null/empty uuid: $pi\n";

    // Also let's check total rows
    $ptot = $pdo->query('SELECT COUNT(*) FROM proformas')->fetchColumn();
    $pitot = $pdo->query('SELECT COUNT(*) FROM proforma_items')->fetchColumn();
    echo "proformas total: $ptot\n";
    echo "proforma_items total: $pitot\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
