<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Sale;
use App\Middleware\AuthMiddleware;
use PDO;

class FinanceController {
    public function index() {
        AuthMiddleware::requireAdmin();
        $pdo = Database::getInstance();
        
        // 1. Total Revenue (All-time paid amount)
        $stmt = $pdo->query("SELECT SUM(paid_amount) FROM sales WHERE voided = 0");
        $totalRevenue = $stmt->fetchColumn() ?: 0;

        // 2. Total Profit (Realized)
        // Realized Profit = Sum over all items: (PaidAmount / TotalAmount) * Qty * (PriceAtSale - ItemCostPrice)
        $stmt = $pdo->query("SELECT SUM((s.paid_amount / s.total_amount) * si.quantity * (si.price_at_sale - i.cost_price)) 
                            FROM sales s 
                            JOIN sale_items si ON s.id = si.sale_id 
                            JOIN items i ON si.item_id = i.id 
                            WHERE s.voided = 0 AND s.total_amount > 0");
        $totalRealizedProfit = $stmt->fetchColumn() ?: 0;

        // 3. Total Expenses (Expenditures)
        $stmt = $pdo->query("SELECT SUM(amount) FROM expenditures");
        $totalExpenses = $stmt->fetchColumn() ?: 0;

        // 4. Coffer Withdrawals
        $stmt = $pdo->query("SELECT SUM(amount) FROM coffer_transactions");
        $totalWithdrawals = $stmt->fetchColumn() ?: 0;

        // 5. Coffer Balance = (Total Paid - Total Expenses - Total Withdrawals)
        $cofferBalance = $totalRevenue - $totalExpenses - $totalWithdrawals;

        // 6. Recent Coffer Transactions
        $stmt = $pdo->query("SELECT ct.*, u.username as recorder_name 
                            FROM coffer_transactions ct 
                            JOIN users u ON ct.recorded_by = u.id 
                            ORDER BY ct.created_at DESC LIMIT 20");
        $transactions = $stmt->fetchAll();

        require __DIR__ . '/../../views/admin/finance/index.php';
    }

    public function withdraw() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = $_POST['amount'] ?? 0;
            $purpose = $_POST['purpose'] ?? '';
            
            if ($amount <= 0 || empty($purpose)) {
                header('Location: ' . BASE_URL . '/admin/finance?error=Invalid amount or purpose');
                exit;
            }

            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("INSERT INTO coffer_transactions (amount, purpose, recorded_by) VALUES (:amount, :purpose, :user_id)");
            $stmt->execute([
                'amount' => $amount,
                'purpose' => $purpose,
                'user_id' => $_SESSION['user_id']
            ]);

            header('Location: ' . BASE_URL . '/admin/finance?success=Withdrawal recorded successfully');
            exit;
        }
    }

    public function update() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $amount = $_POST['amount'] ?? 0;
            $purpose = $_POST['purpose'] ?? '';

            if (!$id || $amount <= 0 || empty($purpose)) {
                header('Location: ' . BASE_URL . '/admin/finance?error=Invalid data provided');
                exit;
            }

            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("UPDATE coffer_transactions SET amount = :amount, purpose = :purpose WHERE id = :id");
            $stmt->execute([
                'amount' => $amount,
                'purpose' => $purpose,
                'id' => $id
            ]);

            header('Location: ' . BASE_URL . '/admin/finance?success=Transaction updated');
            exit;
        }
    }

    public function delete() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                header('Location: ' . BASE_URL . '/admin/finance?error=Missing ID');
                exit;
            }

            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("DELETE FROM coffer_transactions WHERE id = :id");
            $stmt->execute(['id' => $id]);

            header('Location: ' . BASE_URL . '/admin/finance?success=Transaction removed');
            exit;
        }
    }
}
