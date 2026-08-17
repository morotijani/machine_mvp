<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
define('BASE_URL', '/machine_mvp/public');
require_once 'C:\xampp\htdocs\machine_mvp\vendor\autoload.php';
try {
    session_start();
    $pdo = \App\Config\Database::getInstance();
    $c = new \App\Controllers\SyncController($pdo);
    
    // Mock session auth
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'admin';
    
    $c->pushToCloud();
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString();
} catch (\Error $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
