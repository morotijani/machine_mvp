<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\PaymentRequest;
use App\Models\Payment;
use App\Models\Sale;
use App\Middleware\AuthMiddleware;

class CashierController {
    
    public function index() {
        AuthMiddleware::requireLogin();
        // Allow admin and cashier
        // Allow admin, cashier and sales_cashier
        if (!in_array($_SESSION['role'], ['admin', 'cashier', 'sales_cashier'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $pdo = Database::getInstance();
        $prModel = new PaymentRequest($pdo);
        $pendingRequests = $prModel->getPending();
        
        require __DIR__ . '/../../views/cashier/dashboard.php';
    }

    public function apiPending() {
        AuthMiddleware::requireLogin();
        if (!in_array($_SESSION['role'], ['admin', 'cashier', 'sales_cashier'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $pdo = Database::getInstance();
        $prModel = new PaymentRequest($pdo);
        $pendingRequests = $prModel->getPending();
        
        header('Content-Type: application/json');
        echo json_encode(['requests' => $pendingRequests]);
        exit;
    }

    public function process() {
        AuthMiddleware::requireLogin();
        if (!in_array($_SESSION['role'], ['admin', 'cashier', 'sales_cashier'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requestId = $_POST['request_id'];
            $amount = $_POST['amount'];
            $action = $_POST['action']; // 'approve' or 'reject'
            
            $pdo = Database::getInstance();
            $prModel = new PaymentRequest($pdo);
            $request = $prModel->getById($requestId);

            if (!$request || $request['status'] !== 'pending') {
                $_SESSION['error'] = "Payment request is invalid or already processed.";
                header('Location: ' . BASE_URL . '/cashier');
                exit;
            }

            if ($action === 'reject') {
                $prModel->reject($requestId, $_SESSION['user_id']);
                $_SESSION['success'] = "Payment request rejected.";
                header('Location: ' . BASE_URL . '/cashier');
                exit;
            }

            if ($action === 'approve') {
                if ((float)$amount > (float)$request['amount_due']) {
                    $_SESSION['error'] = "Processing Error: Cannot receive more than the requested amount (₵" . number_format($request['amount_due'], 2) . ").";
                    header('Location: ' . BASE_URL . '/cashier');
                    exit;
                }

                try {
                    if ($request['type'] === 'sale' || $request['type'] === 'debt_single') {
                        if ($amount > 0) {
                            $paymentModel = new Payment($pdo);
                            $success = $paymentModel->recordPayment($request['reference_id'], $amount, $_SESSION['user_id']);
                            if ($success === false) {
                                throw new \Exception("Payment record transaction failed.");
                            }
                            $msg = "Payment of ₵" . number_format($amount, 2) . " processed for Invoice #" . $request['reference_id'] . ".";
                        } else {
                            $msg = "Invoice #" . $request['reference_id'] . " endorsed as an Unpaid Draft.";
                        }
                        // Set back URL to cashier dashboard so "Back" button in view.php works
                        $_SESSION['last_sales_url'] = BASE_URL . '/cashier';
                        $redirectUrl = BASE_URL . '/sales/view?id=' . $request['reference_id'] . '&print=true';
                    } elseif ($request['type'] === 'debt_bulk') {
                        $saleModel = new Sale($pdo);
                        $result = $saleModel->repayBulkDebt($request['reference_id'], $amount, $_SESSION['user_id']);
                        if ($result === false) {
                            throw new \Exception("Bulk repayment transaction failed.");
                        }
                        $ids = array_column($result['affected_sales'], 'id');
                        $msg = "Bulk repayment of ₵" . number_format($amount, 2) . " processed. Applied to Invoices: #" . implode(', #', $ids);
                        $redirectUrl = BASE_URL . '/customers/view?id=' . $request['reference_id'];
                    }

                    // Only endorse if the underlying payment succeeded
                    $prModel->endorse($requestId, $_SESSION['user_id']);

                    $_SESSION['success'] = $msg;
                    header('Location: ' . $redirectUrl);
                    exit;

                } catch (\Exception $e) {
                    $_SESSION['error'] = "Processing Error: " . $e->getMessage();
                    header('Location: ' . BASE_URL . '/cashier');
                    exit;
                }
            }
        }
    }
}
