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
        $sql = "SELECT SUM(total_amount) FROM sales WHERE DATE(created_at) = :today";
        $params = ['today' => $today];

        if ($_SESSION['role'] === 'sales') {
            $sql .= " AND user_id = :uid";
            $params['uid'] = $_SESSION['user_id'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $dailySales = $stmt->fetchColumn() ?: 0;

        // 2. Outstanding Debt (Total) - Keep global or customer based? 
        // Usually global debt tracking is fine, or arguably sales person specific. 
        // Requirement: "what he/she has done so far". Debt is tied to customer, which is shared.
        // Salesperson mostly cares about their sales. Let's keep Debt and Low Stock global for now as they are system/inventory status.
        // User asked: "only see update of dashboard upon what he/she has done so far". 
        // This strongly implies SALES stats. Debt/Stock are arguably store-wide properties.
        // Let's filter debt by sales they made? "Money owed on sales I made".
        
        $sqlDebt = "SELECT SUM(total_amount - paid_amount) FROM sales WHERE payment_status != 'paid'";
        $paramsDebt = [];
        if ($_SESSION['role'] === 'sales') {
            $sqlDebt .= " AND user_id = :uid";
            $paramsDebt['uid'] = $_SESSION['user_id'];
        }
        $stmt = $pdo->prepare($sqlDebt);
        $stmt->execute($paramsDebt);
        $totalDebt = $stmt->fetchColumn() ?: 0;

        // 3. Low Stock Items (Count) - Global
        $stmt = $pdo->query("SELECT COUNT(*) FROM items WHERE quantity <= 5");
        $lowStockCount = $stmt->fetchColumn() ?: 0;
        
        // 4. Monthly Stats (for Chart/Table)
        $monthStart = date('Y-m-01');
        $sqlMonthly = "SELECT 
            COUNT(id) as count, 
            SUM(total_amount) as total, 
            SUM(paid_amount) as collected 
            FROM sales WHERE created_at >= :start";
        $paramsMonthly = ['start' => $monthStart];
        
        if ($_SESSION['role'] === 'sales') {
            $sqlMonthly .= " AND user_id = :uid";
            $paramsMonthly['uid'] = $_SESSION['user_id'];
        }

        $stmt = $pdo->prepare($sqlMonthly);
        $stmt->execute($paramsMonthly);
        $monthlyStats = $stmt->fetch();

        // 5. Total Inventory Net Worth (Individual Non-Bundle Items)
        // User asked for non-bundle (type IS NULL or type != 'bundle' if type exists)
        // Our schema uses type = 'bundle' for bundles.
        $stmt = $pdo->query("SELECT SUM(quantity * price) FROM items WHERE (type IS NULL OR type != 'bundle') AND is_deleted = 0");
        $inventoryWorth = $stmt->fetchColumn() ?: 0;

        // 6. Total Worth of Items Sold (Non-Voided)
        $stmt = $pdo->query("SELECT SUM(total_amount) FROM sales WHERE voided = 0");
        $totalSoldWorth = $stmt->fetchColumn() ?: 0;

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
