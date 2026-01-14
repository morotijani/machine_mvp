<?php
namespace App\Controllers;

use App\Config\Database;
use App\Middleware\AuthMiddleware;
use PDO;

class ReportController {
    
    public function dashboard() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();
        $isAdmin = ($_SESSION['role'] === 'admin');

        $today = date('Y-m-d');
        $month = date('m');
        $year = date('Y');
        $uid = $_SESSION['user_id'];
        $isSales = ($_SESSION['role'] === 'sales');

        // Helper for common filters
        $userFilter = $isSales ? " AND user_id = :uid" : "";
        $userFilterS = $isSales ? " AND s.user_id = :uid" : "";
        $params = $isSales ? ['uid' => $uid] : [];

        // 1. Daily Sales (Today)
        $sql = "SELECT SUM(total_amount) FROM sales WHERE DATE(created_at) = :today AND voided = 0" . $userFilter;
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge(['today' => $today], $params));
        $dailySales = $stmt->fetchColumn() ?: 0;

        // 2. Daily Gross Profit (Today)
        $sqlProfit = "SELECT SUM(si.subtotal - (si.quantity * i.cost_price)) 
                      FROM sale_items si 
                      JOIN items i ON si.item_id = i.id 
                      JOIN sales s ON si.sale_id = s.id 
                      WHERE DATE(s.created_at) = :today AND s.voided = 0" . $userFilterS;
        $stmtProfit = $pdo->prepare($sqlProfit);
        $stmtProfit->execute(array_merge(['today' => $today], $params));
        $dailyProfit = $stmtProfit->fetchColumn() ?: 0;

        // 3. Outstanding Debt (Sales-based)
        $sqlDebt = "SELECT SUM(total_amount - paid_amount) FROM sales WHERE payment_status != 'paid' AND voided = 0" . $userFilter;
        $stmt = $pdo->prepare($sqlDebt);
        $stmt->execute($params);
        $salesDebt = $stmt->fetchColumn() ?: 0;

        // 4. Standalone Debt (Global or Filtered?)
        // Debtors are usually global, but if we want "what he/she has done so far", 
        // we might filter by who recorded it. However, Debtor model doesn't have recorded_by for the debtor itself, only repayments.
        // Let's keep it global for now as per previous logic, but sum it with sales debt if needed.
        $debtorModel = new \App\Models\Debtor($pdo);
        $totalStandaloneDebt = $debtorModel->getTotalOutstanding();
        
        $totalDebt = $salesDebt + $totalStandaloneDebt;

        // 5. Low Stock Items (Count) - Global
        $stmt = $pdo->query("SELECT COUNT(*) FROM items WHERE quantity <= 5 AND is_deleted = 0");
        $lowStockCount = $stmt->fetchColumn() ?: 0;

        // 6. Expenditures (Daily & Monthly)
        $expModel = new \App\Models\Expenditure($pdo);
        $userIdExp = $isAdmin ? null : $uid;
        $dailyExpenditures = $expModel->getDailyTotal($today, $userIdExp);
        $monthlyExpenditures = $expModel->getMonthlyTotal($month, $year, $userIdExp);
        $dailyNetProfit = $dailyProfit - $dailyExpenditures;

        // 7. Inventory Net Worth (Admin Only)
        $inventoryWorth = 0;
        $inventoryCost = 0;
        if ($isAdmin) {
            // Retail Value
            $stmt = $pdo->query("SELECT SUM(quantity * price) FROM items WHERE is_deleted = 0");
            $inventoryWorth = $stmt->fetchColumn() ?: 0;
            // Cost Value
            $stmt = $pdo->query("SELECT SUM(quantity * cost_price) FROM items WHERE is_deleted = 0");
            $inventoryCost = $stmt->fetchColumn() ?: 0;
        }

        // 8. Cumulative Lifetime Stats
        $sqlLife = "SELECT 
            COUNT(id) as count, 
            SUM(total_amount) as total, 
            SUM(paid_amount) as collected 
            FROM sales WHERE voided = 0" . $userFilter;
        $stmt = $pdo->prepare($sqlLife);
        $stmt->execute($params);
        $lifetimeStats = $stmt->fetch();

        // 9. Monthly Stats (Current Month)
        $monthStart = date('Y-m-01 00:00:00');
        $sqlMonthly = "SELECT 
            COUNT(id) as count, 
            SUM(total_amount) as total, 
            SUM(paid_amount) as collected 
            FROM sales WHERE created_at >= :start AND voided = 0" . $userFilter;
        $stmt = $pdo->prepare($sqlMonthly);
        $stmt->execute(array_merge(['start' => $monthStart], $params));
        $monthlyStats = $stmt->fetch();

    //     require __DIR__ . '/../../views/dashboard/index.php';
    // }

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
            WHERE YEAR(created_at) = :year AND voided = 0
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
            WHERE YEAR(created_at) = :lastYear AND voided = 0
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
        
        // 4. Daily Reports (Table)
        $stmt = $pdo->query("SELECT DATE(created_at) as sale_date, COUNT(*) as count, SUM(total_amount) as total FROM sales WHERE voided = 0 GROUP BY DATE(created_at) ORDER BY sale_date DESC LIMIT 30");
        $dailyReports = $stmt->fetchAll();
        
        require __DIR__ . '/../../views/reports/index.php';
    }
}
