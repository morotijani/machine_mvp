<?php
namespace App\Controllers;

use App\Config\Database;
use App\Middleware\AuthMiddleware;
use PDO;

class ReportController
{

    public function dashboard()
    {
        AuthMiddleware::requireLogin();
        
        // Allowed: admin, sales_cashier. 
        // Redirect: sales to /sales/create, cashier to /cashier.
        if ($_SESSION['role'] === 'sales') {
            header('Location: ' . BASE_URL . '/sales/create');
            exit;
        }
        if ($_SESSION['role'] === 'cashier') {
            header('Location: ' . BASE_URL . '/cashier');
            exit;
        }

        $pdo = Database::getInstance();
        $isAdmin = ($_SESSION['role'] === 'admin');
        $isLimitedView = ($_SESSION['role'] === 'sales_cashier');

        $today = date('Y-m-d');
        $month = date('m');
        $year = date('Y');
        $uid = $_SESSION['user_id'];
        
        // Helper for common filters
        $userFilter = $isLimitedView ? " AND user_id = :uid" : "";
        $userFilterS = $isLimitedView ? " AND s.user_id = :uid" : "";
        $params = $isLimitedView ? ['uid' => $uid] : [];

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

        // 2.a Realized Today (Collections & Refunds)
        // 1. Total Payments received TODAY (from any sale that is NOT currently voided)
        $sqlPayToday = "SELECT SUM(p.amount) 
                        FROM payments p 
                        JOIN sales s ON p.sale_id = s.id
                        WHERE DATE(p.payment_date) = :today AND s.voided = 0" . ($isLimitedView ? " AND p.recorded_by = :uid" : "");
        $stmtPayToday = $pdo->prepare($sqlPayToday);
        $stmtPayToday->execute(array_merge(['today' => $today], $params));
        $totalPaymentsToday = $stmtPayToday->fetchColumn() ?: 0;

        // 2. Debt Recovered Today (Payments today where sale was created BEFORE today)
        $sqlDebtCol = "SELECT SUM(p.amount) 
               FROM payments p 
               JOIN sales s ON p.sale_id = s.id 
               WHERE DATE(p.payment_date) = :today_pay 
               AND DATE(s.created_at) < :today_sale
               AND s.voided = 0" . ($isLimitedView ? " AND p.recorded_by = :uid" : "");
        $stmtDebtCol = $pdo->prepare($sqlDebtCol);
        $stmtDebtCol->execute(array_merge(['today_pay' => $today, 'today_sale' => $today], $params));
        $todayDebtCollected = $stmtDebtCol->fetchColumn() ?: 0;

        // 3. Gross Today's New Sales Payments (Payments today for sales created today)
        $sqlNewPay = "SELECT SUM(p.amount) 
              FROM payments p 
              JOIN sales s ON p.sale_id = s.id 
              WHERE DATE(p.payment_date) = :today_pay 
              AND DATE(s.created_at) = :today_sale
              AND s.voided = 0" . ($isLimitedView ? " AND p.recorded_by = :uid" : "");
        $stmtNewPay = $pdo->prepare($sqlNewPay);
        $stmtNewPay->execute(array_merge(['today_pay' => $today, 'today_sale' => $today], $params));
        $todayNewSalesGross = $stmtNewPay->fetchColumn() ?: 0;

        // 4. Returns Partitioning
        // 4a. Returns of Today's Sales (New Business returns) - USE cash_refunded instead of total_deduction
        $sqlNewReturns = "SELECT SUM(r.cash_refunded) 
                  FROM sale_returns r 
                  JOIN sales s ON r.sale_id = s.id 
                  WHERE DATE(r.created_at) = :today_ret 
                  AND DATE(s.created_at) = :today_sale" . ($isLimitedView ? " AND r.recorded_by = :uid" : "");
        $stmtNewReturns = $pdo->prepare($sqlNewReturns);
        $stmtNewReturns->execute(array_merge(['today_ret' => $today, 'today_sale' => $today], $params));
        $todayReturnsValueNew = $stmtNewReturns->fetchColumn() ?: 0;

        // 4b. Total Returns processed TODAY (Global/User-filtered) - USE cash_refunded for net collections
        $sqlTotalReturns = "SELECT SUM(cash_refunded) FROM sale_returns WHERE DATE(created_at) = :today_return" . ($isLimitedView ? " AND recorded_by = :uid" : "");
        $stmtTotalReturns = $pdo->prepare($sqlTotalReturns);
        $stmtTotalReturns->execute(array_merge(['today_return' => $today], $params));
        $totalReturnsToday = $stmtTotalReturns->fetchColumn() ?: 0;

        // 4c. Refunds from VOIDED sales today
        // If a sale was voided today, we assume the physical cash was refunded today.
        $sqlVoidedToday = "SELECT SUM(paid_amount) FROM sales WHERE voided = 1 AND DATE(voided_at) = :today" . $userFilter;
        $stmtVoidedToday = $pdo->prepare($sqlVoidedToday);
        $stmtVoidedToday->execute(array_merge(['today' => $today], $params));
        $voidedRefundsToday = $stmtVoidedToday->fetchColumn() ?: 0;

        // Net Cash from New Sales = Gross Payments for Today's Sales - Returns of Today's Sales
        $todayNewSalesCollected = $todayNewSalesGross - $todayReturnsValueNew;

        // Total Net Collections = All payments today - all returns today - all voided refunds today
        $totalNetCollections = $totalPaymentsToday - $totalReturnsToday - $voidedRefundsToday;

        // 2.b Realized Gross Profit (Actual earned profit from collections)
        // Formula: SUM( (payment_amount / sale_total) * sale_potential_profit )
        // This naturally accounts for returns because total_amount is reduced in the denominator.
        $sqlRealizedProfit = "
    SELECT SUM(
        (p.amount / s.total_amount) * 
        (SELECT SUM(si.quantity * (si.price_at_sale - i.cost_price)) 
         FROM sale_items si 
         JOIN items i ON si.item_id = i.id 
         WHERE si.sale_id = s.id)
    )
    FROM payments p
    JOIN sales s ON p.sale_id = s.id
    WHERE DATE(p.payment_date) = :today AND s.voided = 0 AND s.total_amount > 0
" . ($isLimitedView ? " AND p.recorded_by = :uid" : "");
        $stmtRealized = $pdo->prepare($sqlRealizedProfit);
        $stmtRealized->execute(array_merge(['today' => $today], $params));
        $todayRealizedProfit = $stmtRealized->fetchColumn() ?: 0;

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

        // Realized Net Profit (Actual Cash Profit minus Expenses)
        $todayRealizedNetProfit = $todayRealizedProfit - $dailyExpenditures;

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

        // 10. Today's Returns List (Broken down by item)
        $sqlTodayReturns = "
            SELECT 
                i.name as item_name, 
                ri.quantity, 
                (ri.quantity * ri.price_at_sale) as deduction,
                u.username as salesperson,
                DATE_FORMAT(r.created_at, '%H:%i') as return_time
            FROM sale_return_items ri
            JOIN sale_returns r ON ri.return_id = r.id
            JOIN items i ON ri.item_id = i.id
            JOIN users u ON r.recorded_by = u.id
            WHERE DATE(r.created_at) = :today" . ($isLimitedView ? " AND r.recorded_by = :uid" : "") . "
            ORDER BY r.created_at DESC
        ";
        $stmtTodayReturns = $pdo->prepare($sqlTodayReturns);
        $stmtTodayReturns->execute(array_merge(['today' => $today], $params));
        $todayReturnedItemsList = $stmtTodayReturns->fetchAll();

        require __DIR__ . '/../../views/dashboard/index.php';
    }
    public function index()
    {
        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        $pdo = Database::getInstance();

        // Get selected year or default to current
        $selectedYear = $_GET['year'] ?? date('Y');

        // 1. Get Available Years for Filter
        $stmt = $pdo->query("SELECT DISTINCT YEAR(created_at) as year FROM sales ORDER BY year DESC");
        $availableYears = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (empty($availableYears))
            $availableYears = [date('Y')];

        // 2. Monthly Stats for Selected Year (Graph Data & Profit)
        $monthlySales = array_fill(1, 12, 0);
        $monthlyProfits = array_fill(1, 12, 0);

        $stmt = $pdo->prepare("
            SELECT 
                MONTH(s.created_at) as month, 
                SUM(si.subtotal) as total,
                SUM((s.paid_amount / s.total_amount) * si.quantity * (si.price_at_sale - i.cost_price)) as profit
            FROM sales s
            JOIN sale_items si ON s.id = si.sale_id
            JOIN items i ON si.item_id = i.id
            WHERE YEAR(s.created_at) = :year AND s.voided = 0 AND s.total_amount > 0
            GROUP BY MONTH(s.created_at)
        ");
        $stmt->execute(['year' => $selectedYear]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            $monthlySales[$row['month']] = $row['total'];
            $monthlyProfits[$row['month']] = $row['profit'];
        }

        // 2.a Customer Retention (Repeat Rate)
        // Ratio of customers with more than 1 sale to total unique customers
        $stmtRet = $pdo->query("
            SELECT 
                (SELECT COUNT(*) FROM (SELECT customer_id FROM sales WHERE customer_id IS NOT NULL AND voided = 0 GROUP BY customer_id HAVING COUNT(*) > 1) as repeats) as repeat_customers,
                (SELECT COUNT(DISTINCT customer_id) FROM sales WHERE customer_id IS NOT NULL AND voided = 0) as total_customers
        ");
        $retentionData = $stmtRet->fetch(PDO::FETCH_ASSOC);
        $totalCustomers = $retentionData['total_customers'] ?: 1; // Avoid div by zero
        $repeatCustomers = $retentionData['repeat_customers'] ?: 0;
        $retentionRate = ($repeatCustomers / $totalCustomers) * 100;

        // 2.b Inventory Turnover Ratio (Annual Approximation)
        // Formula: COGS / Average Inventory
        // COGS = Cost of goods sold during the selected year
        $stmtCOGS = $pdo->prepare("
            SELECT SUM(si.quantity * i.cost_price) 
            FROM sale_items si 
            JOIN items i ON si.item_id = i.id 
            JOIN sales s ON si.sale_id = s.id
            WHERE YEAR(s.created_at) = :year AND s.voided = 0
        ");
        $stmtCOGS->execute(['year' => $selectedYear]);
        $annualCOGS = $stmtCOGS->fetchColumn() ?: 0;

        // Ending Inventory = Current Stock Value (Cost)
        $stmtStockCost = $pdo->query("SELECT SUM(quantity * cost_price) FROM items WHERE is_deleted = 0");
        $endingInventory = $stmtStockCost->fetchColumn() ?: 0;

        // Approximation: Beginning Inventory = Ending Inventory + Sold items cost (not perfect but standard for static data)
        $beginningInventorySimple = $endingInventory + $annualCOGS;
        $avgInventory = ($beginningInventorySimple + $endingInventory) / 2;

        $inventoryTurnover = ($avgInventory > 0) ? ($annualCOGS / $avgInventory) : 0;

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
                SUM((s.paid_amount / s.total_amount) * si.quantity * (si.price_at_sale - i.cost_price)) as profit,
                (SELECT SUM(amount) FROM expenditures WHERE DATE(date) = DATE(s.created_at)) as total_expenditure
            FROM sales s
            JOIN sale_items si ON s.id = si.sale_id
            JOIN items i ON si.item_id = i.id
            WHERE s.voided = 0 AND s.total_amount > 0
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

        // 6. Top Selling Items (By Volume)
        $stmtTopQty = $pdo->prepare("
            SELECT i.name, i.sku, SUM(si.quantity) as total_qty, SUM(si.subtotal) as total_revenue
            FROM sale_items si
            JOIN items i ON si.item_id = i.id
            JOIN sales s ON si.sale_id = s.id
            WHERE s.voided = 0 AND YEAR(s.created_at) = :year
            GROUP BY si.item_id
            ORDER BY total_qty DESC
            LIMIT 5
        ");
        $stmtTopQty->execute(['year' => $selectedYear]);
        $topSellingItems = $stmtTopQty->fetchAll(PDO::FETCH_ASSOC);

        // 7. Top Revenue Items
        $stmtTopRev = $pdo->prepare("
            SELECT i.name, i.sku, SUM(si.subtotal) as total_revenue
            FROM sale_items si
            JOIN items i ON si.item_id = i.id
            JOIN sales s ON si.sale_id = s.id
            WHERE s.voided = 0 AND YEAR(s.created_at) = :year
            GROUP BY si.item_id
            ORDER BY total_revenue DESC
            LIMIT 5
        ");
        $stmtTopRev->execute(['year' => $selectedYear]);
        $topRevenueItems = $stmtTopRev->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../views/reports/index.php';
    }

    public function export()
    {
        AuthMiddleware::requireAdmin();
        $pdo = Database::getInstance();
        $type = $_GET['type'] ?? 'sales';
        $year = $_GET['year'] ?? date('Y');

        $filename = "report_{$type}_{$year}_" . date('Ymd') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        if ($type === 'monthly_comparison') {
            fputcsv($output, ['Month', 'Last Year Sales', 'Current Year Sales', 'Profit', 'Difference', 'Growth %']);

            // Logic similar to index() to get data
            $monthlySales = array_fill(1, 12, 0);
            $stmt = $pdo->prepare("SELECT MONTH(created_at) as month, SUM(total_amount) as total FROM sales WHERE YEAR(created_at) = :year AND voided = 0 GROUP BY MONTH(created_at)");
            $stmt->execute(['year' => $year]);
            $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            foreach ($results as $m => $t)
                $monthlySales[$m] = $t;

            $lastYear = $year - 1;
            $stmt = $pdo->prepare("SELECT MONTH(created_at) as month, SUM(total_amount) as total FROM sales WHERE YEAR(created_at) = :lastYear AND voided = 0 GROUP BY MONTH(created_at)");
            $stmt->execute(['lastYear' => $lastYear]);
            $lastYearResults = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            for ($m = 1; $m <= 12; $m++) {
                $cur = $monthlySales[$m] ?? 0;
                $prev = $lastYearResults[$m] ?? 0;
                $diff = $cur - $prev;
                $growth = ($prev > 0) ? ($diff / $prev) * 100 : 0;
                fputcsv($output, [date('F', mktime(0, 0, 0, $m, 1)), $prev, $cur, 'N/A', $diff, round($growth, 2) . '%']);
            }
        }
        else {
            // Default: Daily Sales last 30 days
            fputcsv($output, ['Date', 'Sales Count', 'Total Revenue']);
            $stmt = $pdo->query("SELECT DATE(created_at) as d, COUNT(*) as c, SUM(total_amount) as t FROM sales WHERE voided = 0 GROUP BY d ORDER BY d DESC LIMIT 100");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;
    }

    public function daily()
    {
        AuthMiddleware::requireAdmin();
        $pdo = Database::getInstance();
        $date = $_GET['date'] ?? date('Y-m-d');

        // 1. Financial Summary for the Date
        // a. Daily Sales (Invoiced)
        $stmt = $pdo->prepare("SELECT SUM(total_amount) FROM sales WHERE DATE(created_at) = :date AND voided = 0");
        $stmt->execute(['date' => $date]);
        $dailySales = $stmt->fetchColumn() ?: 0;

        // b. Total Payments Received Today (Any sale that is NOT currently voided)
        $stmt = $pdo->prepare("
            SELECT SUM(p.amount) 
            FROM payments p 
            JOIN sales s ON p.sale_id = s.id 
            WHERE DATE(p.payment_date) = :date AND s.voided = 0
        ");
        $stmt->execute(['date' => $date]);
        $totalPaymentsToday = $stmt->fetchColumn() ?: 0;

        // c. Debt Recovered (Sales) - Payments for OLD sales
        $stmt = $pdo->prepare("
            SELECT SUM(p.amount) 
            FROM payments p 
            JOIN sales s ON p.sale_id = s.id 
            WHERE DATE(p.payment_date) = :date AND DATE(s.created_at) < :date_ref AND s.voided = 0
        ");
        $stmt->execute(['date' => $date, 'date_ref' => $date]);
        $debtRecoveredSales = $stmt->fetchColumn() ?: 0;

        // d. Standalone Debt Activity (New debt created today)
        $stmt = $pdo->prepare("SELECT SUM(total_amount) FROM standalone_debtors WHERE DATE(created_at) = :date AND is_deleted = 0");
        $stmt->execute(['date' => $date]);
        $newStandaloneDebt = $stmt->fetchColumn() ?: 0;

        // e. Standalone Debt Repayments (Cash collected today for standalone debt)
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM debt_repayments WHERE DATE(payment_date) = :date");
        $stmt->execute(['date' => $date]);
        $standaloneRepayments = $stmt->fetchColumn() ?: 0;

        // f. Total Expenditure
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM expenditures WHERE date = :date AND is_deleted = 0");
        $stmt->execute(['date' => $date]);
        $dailyExpenditure = $stmt->fetchColumn() ?: 0;

        // g. Realized Gross Profit (Based on payments today)
        // Using common logic: realized profit = (payment / total) * (selling - cost)
        $stmt = $pdo->prepare("
            SELECT SUM(
                (p.amount / NULLIF(s.total_amount, 0)) * 
                (SELECT SUM(si.quantity * (si.price_at_sale - i.cost_price)) 
                 FROM sale_items si 
                 JOIN items i ON si.item_id = i.id 
                 WHERE si.sale_id = s.id)
            )
            FROM payments p
            JOIN sales s ON p.sale_id = s.id
            WHERE DATE(p.payment_date) = :date AND s.voided = 0
        ");
        $stmt->execute(['date' => $date]);
        $realizedGrossProfit = $stmt->fetchColumn() ?: 0;

        // h. Potential Profit
        $stmt = $pdo->prepare("
            SELECT SUM(si.subtotal - (si.quantity * i.cost_price)) 
            FROM sale_items si 
            JOIN items i ON si.item_id = i.id 
            JOIN sales s ON si.sale_id = s.id 
            WHERE DATE(s.created_at) = :date AND s.voided = 0
        ");
        $stmt->execute(['date' => $date]);
        $potentialProfit = $stmt->fetchColumn() ?: 0;

        // i. Returns handled today (Actual Cash Refunded)
        $stmt = $pdo->prepare("SELECT SUM(cash_refunded) FROM sale_returns WHERE DATE(created_at) = :date");
        $stmt->execute(['date' => $date]);
        $todayReturns = $stmt->fetchColumn() ?: 0;

        // j. Refunds from VOIDED sales on this date
        $stmt = $pdo->prepare("SELECT SUM(paid_amount) FROM sales WHERE voided = 1 AND DATE(voided_at) = :date");
        $stmt->execute(['date' => $date]);
        $voidedRefunds = $stmt->fetchColumn() ?: 0;

        // 2. Collections (Breakdown)
        // Today's New Sales (Invoices created today)
        $stmt = $pdo->prepare("
            SELECT s.*, c.name as customer_name, u.username as recorder
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            LEFT JOIN users u ON s.user_id = u.id
            WHERE DATE(s.created_at) = :date AND s.voided = 0
            ORDER BY s.created_at DESC
        ");
        $stmt->execute(['date' => $date]);
        $todayInvoicesList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debt recovered list (Sales)
        $stmt = $pdo->prepare("
            SELECT s.id, p.amount, c.name as customer_name, p.payment_date, u.username as recorder
            FROM payments p
            JOIN sales s ON p.sale_id = s.id
            LEFT JOIN customers c ON s.customer_id = c.id
            LEFT JOIN users u ON p.recorded_by = u.id
            WHERE DATE(p.payment_date) = :date AND DATE(s.created_at) < :date_ref AND s.voided = 0
        ");
        $stmt->execute(['date' => $date, 'date_ref' => $date]);
        $debtRecoveredList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. New Standalone Debtors created this day
        $stmt = $pdo->prepare("SELECT * FROM standalone_debtors WHERE DATE(created_at) = :date AND is_deleted = 0");
        $stmt->execute(['date' => $date]);
        $newDebtorsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Standalone Repayments list
        $stmt = $pdo->prepare("
            SELECT dr.*, d.name as debtor_name, u.username as recorder
            FROM debt_repayments dr
            JOIN standalone_debtors d ON dr.debtor_id = d.id
            LEFT JOIN users u ON dr.recorded_by = u.id
            WHERE DATE(dr.payment_date) = :date
        ");
        $stmt->execute(['date' => $date]);
        $standaloneRepaymentsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Returned Items List
        $stmt = $pdo->prepare("
            SELECT i.name, ri.quantity, ri.price_at_sale, (ri.quantity * ri.price_at_sale) as total, s.id as sale_id, u.username as recorder
            FROM sale_return_items ri
            JOIN sale_returns r ON ri.return_id = r.id
            JOIN items i ON ri.item_id = i.id
            JOIN sales s ON r.sale_id = s.id
            LEFT JOIN users u ON r.recorded_by = u.id
            WHERE DATE(r.created_at) = :date
        ");
        $stmt->execute(['date' => $date]);
        $returnedItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 6. System Logs for the Date
        $stmt = $pdo->prepare("
            SELECT il.*, u.username as operator_name, i.name as item_name
            FROM item_logs il
            LEFT JOIN users u ON il.user_id = u.id
            LEFT JOIN items i ON il.item_id = i.id
            WHERE DATE(il.created_at) = :date
            ORDER BY il.created_at DESC
        ");
        $stmt->execute(['date' => $date]);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 7. Expenditures list
        $stmt = $pdo->prepare("
            SELECT e.*, u.username as recorder
            FROM expenditures e
            LEFT JOIN users u ON e.recorded_by = u.id
            WHERE DATE(e.date) = :date AND e.is_deleted = 0
        ");
        $stmt->execute(['date' => $date]);
        $expendituresList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Final Calculations
        $totalNetCollection = $totalPaymentsToday + $standaloneRepayments - $todayReturns - $voidedRefunds;
        $realizedNetProfit = $realizedGrossProfit - $dailyExpenditure;
        $dailyNetProfit = $potentialProfit - $dailyExpenditure;

        require __DIR__ . '/../../views/reports/daily.php';
    }
}
