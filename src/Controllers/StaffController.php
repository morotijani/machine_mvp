<?php
namespace App\Controllers;

use App\Config\Database;
use App\Middleware\AuthMiddleware;
use PDO;

class StaffController {
    
    public function index() {
        AuthMiddleware::requireAdmin();
        $pdo = Database::getInstance();

        // Fetch all sales staff with summary stats
        $sql = "SELECT 
                    u.id, 
                    u.username, 
                    u.fullname, 
                    u.role,
                    u.profile_image,
                    u.created_at,
                    (SELECT COUNT(*) FROM sales WHERE user_id = u.id AND voided = 0) as sales_count,
                    (SELECT SUM(total_amount) FROM sales WHERE user_id = u.id AND voided = 0) as total_revenue,
                    (SELECT SUM(p.amount) FROM payments p JOIN sales s ON p.sale_id = s.id WHERE s.user_id = u.id AND s.voided = 0) as total_collected,
                    (SELECT SUM(amount) FROM expenditures WHERE recorded_by = u.id AND is_deleted = 0) as total_expenses
                FROM users u
                WHERE u.is_deleted = 0
                ORDER BY u.created_at DESC";
        
        $stmt = $pdo->query($sql);
        $staff = $stmt->fetchAll();

        require __DIR__ . '/../../views/admin/staff/index.php';
    }

    public function detail() {
        AuthMiddleware::requireAdmin();
        $pdo = Database::getInstance();
        $uid = $_GET['id'] ?? null;

        if (!$uid) {
            header('Location: ' . BASE_URL . '/admin/staff');
            exit;
        }

        // Store current URL for "Back" button persistence on invoices
        $_SESSION['last_sales_url'] = $_SERVER['REQUEST_URI'];

        // 1. Staff Info
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$uid]);
        $user = $stmt->fetch();

        if (!$user) {
            header('Location: ' . BASE_URL . '/admin/staff');
            exit;
        }

        // 2. Performance Summary
        $sqlStats = "SELECT 
            COUNT(id) as count, 
            SUM(total_amount) as total, 
            SUM(paid_amount) as collected 
            FROM sales WHERE user_id = ? AND voided = 0";
        $stmt = $pdo->prepare($sqlStats);
        $stmt->execute([$uid]);
        $stats = $stmt->fetch();

        // 3. Profit Calculation
        $sqlProfit = "SELECT SUM(si.subtotal - (si.quantity * i.cost_price)) as profit
                      FROM sale_items si 
                      JOIN items i ON si.item_id = i.id 
                      JOIN sales s ON si.sale_id = s.id 
                      WHERE s.user_id = ? AND s.voided = 0";
        $stmt = $pdo->prepare($sqlProfit);
        $stmt->execute([$uid]);
        $profit = $stmt->fetchColumn() ?: 0;

        // 4. Expenses
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM expenditures WHERE recorded_by = ? AND is_deleted = 0");
        $stmt->execute([$uid]);
        $expenses = $stmt->fetchColumn() ?: 0;

        // 5. Today's Stats
        $today = date('Y-m-d');
        
        $sqlTodaySales = "SELECT COUNT(*) as count, SUM(total_amount) as revenue, SUM(paid_amount) as collected 
                          FROM sales WHERE user_id = ? AND voided = 0 AND DATE(created_at) = ?";
        $stmt = $pdo->prepare($sqlTodaySales);
        $stmt->execute([$uid, $today]);
        $todayBasic = $stmt->fetch();

        $sqlTodayProfit = "SELECT SUM(si.subtotal - (si.quantity * i.cost_price)) as profit
                           FROM sale_items si 
                           JOIN items i ON si.item_id = i.id 
                           JOIN sales s ON si.sale_id = s.id 
                           WHERE s.user_id = ? AND s.voided = 0 AND DATE(s.created_at) = ?";
        $stmt = $pdo->prepare($sqlTodayProfit);
        $stmt->execute([$uid, $today]);
        $todayProfit = $stmt->fetchColumn() ?: 0;

        $sqlTodayExpenses = "SELECT SUM(amount) FROM expenditures WHERE recorded_by = ? AND is_deleted = 0 AND DATE(date) = ?";
        $stmt = $pdo->prepare($sqlTodayExpenses);
        $stmt->execute([$uid, $today]);
        $todayExpenses = $stmt->fetchColumn() ?: 0;

        $todayStats = [
            'count' => $todayBasic['count'] ?: 0,
            'revenue' => $todayBasic['revenue'] ?: 0,
            'collected' => $todayBasic['collected'] ?: 0,
            'profit' => $todayProfit ?: 0,
            'expenses' => $todayExpenses ?: 0,
            'net' => ($todayProfit ?: 0) - ($todayExpenses ?: 0)
        ];

        // 6. Activity Log (Recent Sales)
        $stmt = $pdo->prepare("SELECT * FROM sales WHERE user_id = ? AND voided = 0 ORDER BY created_at DESC LIMIT 10");
        $stmt->execute([$uid]);
        $recentSales = $stmt->fetchAll();

        // 6. Login History
        $stmt = $pdo->prepare("SELECT * FROM user_logins WHERE user_id = ? ORDER BY login_at DESC LIMIT 20");
        $stmt->execute([$uid]);
        $loginHistory = $stmt->fetchAll();

        require __DIR__ . '/../../views/admin/staff/detail.php';
    }
}
