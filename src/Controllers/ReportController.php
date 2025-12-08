<?php
namespace App\Controllers;

use App\Config\Database;
use App\Middleware\AuthMiddleware;
use PDO;

class ReportController {
    
    public function dashboard() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();

        // 1. Total Daily Sales (Today)
        $today = date('Y-m-d');
        $stmt = $pdo->prepare("SELECT SUM(total_amount) FROM sales WHERE DATE(created_at) = :today");
        $stmt->execute(['today' => $today]);
        $dailySales = $stmt->fetchColumn() ?: 0;

        // 2. Outstanding Debt (Total)
        $stmt = $pdo->query("SELECT SUM(total_amount - paid_amount) FROM sales WHERE payment_status != 'paid'");
        $totalDebt = $stmt->fetchColumn() ?: 0;

        // 3. Low Stock Items (Count)
        $stmt = $pdo->query("SELECT COUNT(*) FROM items WHERE quantity <= 5");
        $lowStockCount = $stmt->fetchColumn() ?: 0;
        
        // 4. Monthly Stats (for Chart/Table)
        $monthStart = date('Y-m-01');
        $stmt = $pdo->prepare("SELECT 
            COUNT(id) as count, 
            SUM(total_amount) as total, 
            SUM(paid_amount) as collected 
            FROM sales WHERE created_at >= :start");
        $stmt->execute(['start' => $monthStart]);
        $monthlyStats = $stmt->fetch();

        require __DIR__ . '/../../views/dashboard/index.php';
    }
}
