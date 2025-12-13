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
    public function index() {
        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();
        
        $pdo = Database::getInstance();
        
        // Get selected year or default to current
        $selectedYear = $_GET['year'] ?? date('Y');
        
        // 1. Get Available Years for Filter
        $stmt = $pdo->query("SELECT DISTINCT YEAR(created_at) as year FROM sales ORDER BY year DESC");
        $availableYears = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (empty($availableYears)) $availableYears = [date('Y')];

        // 2. Monthly Stats for Selected Year (Graph Data)
        // Initialize all 12 months with 0
        $monthlySales = array_fill(1, 12, 0);
        
        $stmt = $pdo->prepare("
            SELECT MONTH(created_at) as month, SUM(total_amount) as total 
            FROM sales 
            WHERE YEAR(created_at) = :year 
            GROUP BY MONTH(created_at)
        ");
        $stmt->execute(['year' => $selectedYear]);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Merge results into empty months array
        foreach ($results as $month => $total) {
            $monthlySales[$month] = $total;
        }

        // 3. Comparison Data (Last Year vs Selected Year)
        $lastYear = $selectedYear - 1;
        $comparisonData = [];
        
        // Fetch Last Year Data
        $stmt = $pdo->prepare("
            SELECT MONTH(created_at) as month, SUM(total_amount) as total 
            FROM sales 
            WHERE YEAR(created_at) = :lastYear 
            GROUP BY MONTH(created_at)
        ");
        $stmt->execute(['lastYear' => $lastYear]);
        $lastYearResults = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        for ($m = 1; $m <= 12; $m++) {
            $currentVal = $monthlySales[$m] ?? 0;
            $lastVal = $lastYearResults[$m] ?? 0;
            
            $comparisonData[$m] = [
                'month_name' => date('F', mktime(0, 0, 0, $m, 1)),
                'current_year' => $currentVal,
                'last_year' => $lastVal,
                'difference' => $currentVal - $lastVal,
                'growth' => ($lastVal > 0) ? (($currentVal - $lastVal) / $lastVal) * 100 : 0
            ];
        }
        
        // Keep the old daily reports for legacy/reference if needed, or remove. 
        // User asked for "monthly sales... table view of last year and current year", 
        // so maybe replace the daily one or keep it at bottom. Let's keep it but maybe minimize it.
        $stmt = $pdo->query("SELECT DATE(created_at) as sale_date, COUNT(*) as count, SUM(total_amount) as total FROM sales GROUP BY DATE(created_at) ORDER BY sale_date DESC LIMIT 30");
        $dailyReports = $stmt->fetchAll();
        
        require __DIR__ . '/../../views/reports/index.php';
    }
}
