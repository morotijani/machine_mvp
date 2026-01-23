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

        // 2. Monthly Stats for Selected Year (Graph Data & Profit)
        $monthlySales = array_fill(1, 12, 0);
        $monthlyProfits = array_fill(1, 12, 0);
        
        $stmt = $pdo->prepare("
            SELECT 
                MONTH(s.created_at) as month, 
                SUM(s.total_amount) as total,
                SUM(si.subtotal - (si.quantity * i.cost_price)) as profit
            FROM sales s
            JOIN sale_items si ON s.id = si.sale_id
            JOIN items i ON si.item_id = i.id
            WHERE YEAR(s.created_at) = :year AND s.voided = 0
            GROUP BY MONTH(s.created_at)
        ");
        $stmt->execute(['year' => $selectedYear]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $row) {
            $monthlySales[$row['month']] = $row['total'];
            $monthlyProfits[$row['month']] = $row['profit'];
        }

        // 3. Comparison Data (Last Year vs Selected Year)
        $lastYear = $selectedYear - 1;
        $comparisonData = [];
        
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
            $currentProfit = $monthlyProfits[$m] ?? 0;
            $lastVal = $lastYearResults[$m] ?? 0;
            
            $comparisonData[$m] = [
                'month_name' => date('F', mktime(0, 0, 0, $m, 1)),
                'current_year' => $currentVal,
                'current_profit' => $currentProfit,
                'last_year' => $lastVal,
                'difference' => $currentVal - $lastVal,
                'growth' => ($lastVal > 0) ? (($currentVal - $lastVal) / $lastVal) * 100 : 0,
                'profit_margin' => ($currentVal > 0) ? ($currentProfit / $currentVal) * 100 : 0
            ];
        }
        
        // 4. Daily Reports (Table)
        $sqlDaily = "
            SELECT 
                DATE(s.created_at) as sale_date, 
                COUNT(DISTINCT s.id) as count, 
                SUM(si.subtotal) as total,
                SUM(si.subtotal - (si.quantity * i.cost_price)) as profit,
                (SELECT SUM(amount) FROM expenditures WHERE DATE(date) = DATE(s.created_at)) as total_expenditure
            FROM sales s
            JOIN sale_items si ON s.id = si.sale_id
            JOIN items i ON si.item_id = i.id
            WHERE s.voided = 0 
            GROUP BY DATE(s.created_at) 
            ORDER BY sale_date DESC 
            LIMIT 30
        ";
        $stmt = $pdo->query($sqlDaily);
        $dailyReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Calculate Remaining Inventory Value (Backwards)
        // Current Retail Value of all items
        $stmtWorth = $pdo->query("SELECT SUM(quantity * price) FROM items WHERE is_deleted = 0");
        $currentValue = $stmtWorth->fetchColumn() ?: 0;
        
        $runningValue = $currentValue;
        // Since dailyReports is ordered by sale_date DESC (newest first), 
        // the FIRST entry is "end of today" (which is currentValue).
        // Each PREVIOUS entry should be the following day's value PLUS that day's sales.
        foreach ($dailyReports as &$report) {
            $report['remaining_inventory_value'] = $runningValue;
            $runningValue += ($report['total'] ?? 0);
        }
        unset($report); // break reference
        
        require __DIR__ . '/../../views/reports/index.php';
    }
}
