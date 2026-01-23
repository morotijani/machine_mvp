<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Sale;
use App\Models\Item;
use App\Models\Customer;
use App\Middleware\AuthMiddleware;
use Exception;

class SaleController {
    
    public function index() {
        AuthMiddleware::requireLogin();
        
        // Store current URL with filters for "Back" button persistence
        $_SESSION['last_sales_url'] = $_SERVER['REQUEST_URI'];

        $pdo = Database::getInstance();
        $saleModel = new Sale($pdo);
        
        // Filters
        $filters = [
            'search' => $_GET['search'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'status' => $_GET['status'] ?? 'all',
            'delete_request' => $_GET['delete_request'] ?? '',
            'show_voided' => $_GET['show_voided'] ?? 'no'
        ];

        // Restrict non-admins to their own sales
        if ($_SESSION['role'] !== 'admin') {
            $filters['user_id'] = $_SESSION['user_id'];
        }

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;

        $totalRecords = $saleModel->countAll($filters);
        $totalPages = ceil($totalRecords / $limit);
        
        // Ensure page is valid
        if ($page < 1) $page = 1;
        if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

        $sales = $saleModel->getAll($filters, $page, $limit);
        
        require __DIR__ . '/../../views/sales/index.php';
    }

    public function create() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Decode JSON input if sent as JSON, or handle Form Data
            // We'll assume Form Data for simplicity or JSON if needed.
            // Let's support JSON for the complex cart structure
            $input = json_decode(file_get_contents('php://input'), true);

            if ($input) {
                try {
                    // Idempotency check: prevent duplicate submissions within 5 seconds
                    $requestHash = md5(json_encode($input));
                    $lastSubmission = $_SESSION['last_sale_submission'] ?? null;
                    
                    if ($lastSubmission && $lastSubmission['hash'] === $requestHash && (time() - $lastSubmission['time'] < 5)) {
                        // Return the previous sale ID
                        echo json_encode(['success' => true, 'sale_id' => $lastSubmission['sale_id'], 'is_duplicate' => true]);
                        exit;
                    }

                    $saleModel = new Sale($pdo);
                    $saleId = $saleModel->createSale(
                        $input['customer_id'], 
                        $_SESSION['user_id'], 
                        $input['items'], 
                        $input['payment_amount']
                    );

                    // Store submission record
                    $_SESSION['last_sale_submission'] = [
                        'hash' => $requestHash,
                        'time' => time(),
                        'sale_id' => $saleId
                    ];

                    echo json_encode(['success' => true, 'sale_id' => $saleId]);
                } catch (Exception $e) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
                exit;
            }
        }

        // Prepare data for the View
        $itemModel = new Item($pdo);
        $customerModel = new Customer($pdo);
        $items = $itemModel->getAll();
        $customers = $customerModel->getAll();
        
        require __DIR__ . '/../../views/sales/create.php';
    }

    public function view() {
        AuthMiddleware::requireLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/sales');
            exit;
        }

        $pdo = Database::getInstance();
        $saleModel = new Sale($pdo);
        $sale = $saleModel->getById($id);

        if (!$sale) {
            header('Location: ' . BASE_URL . '/sales');
            exit;
        }

        // Fetch payment history
        $stmt = $pdo->prepare("SELECT p.*, u.username FROM payments p JOIN users u ON p.recorded_by = u.id WHERE p.sale_id = :sid ORDER BY p.payment_date DESC");
        $stmt->execute(['sid' => $id]);
        $payments = $stmt->fetchAll();

        // Fetch Return history
        $stmtRet = $pdo->prepare("SELECT sr.*, u.username as returner_name FROM sale_returns sr JOIN users u ON sr.recorded_by = u.id WHERE sr.sale_id = :sid ORDER BY sr.created_at DESC");
        $stmtRet->execute(['sid' => $id]);
        $returns = $stmtRet->fetchAll();

        foreach ($returns as &$row) {
            $stmtRI = $pdo->prepare("SELECT sri.*, i.name as item_name FROM sale_return_items sri JOIN items i ON sri.item_id = i.id WHERE sri.return_id = :rid");
            $stmtRI->execute(['rid' => $row['id']]);
            $row['details'] = $stmtRI->fetchAll();
        }

        // Fetch Settings
        $settingModel = new \App\Models\Setting($pdo);
        $settings = $settingModel->get();

        $returnUrl = $_SESSION['last_sales_url'] ?? (BASE_URL . '/sales');

        require __DIR__ . '/../../views/sales/view.php';
    }

    public function pay() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $saleId = $_POST['sale_id'];
            $amount = $_POST['amount'];
            
            $pdo = Database::getInstance();
            $paymentModel = new \App\Models\Payment($pdo);
            $paymentModel->recordPayment($saleId, $amount, $_SESSION['user_id']);
            
            header('Location: ' . BASE_URL . '/sales/view?id=' . $saleId);
            exit;
        }
    }
    public function requestDelete() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $saleId = $_POST['sale_id'];
            $pdo = Database::getInstance();
            $saleModel = new Sale($pdo);
            
            // Verify ownership (optional: prevent deleting others' sales unless admin, but requirement said 'their purchase')
            // For now, we assume the UI handles visibility, but backend check is better.
            $sale = $saleModel->getById($saleId);
            if ($sale['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] === 'admin') {
                $saleModel->requestDelete($saleId);
            }
            
            header('Location: ' . BASE_URL . '/sales');
            exit;
        }
    }

    public function processDeleteRequest() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $saleId = $_POST['sale_id'];
            $action = $_POST['action']; // approve or reject
            
            $pdo = Database::getInstance();
            $saleModel = new Sale($pdo);
            
            if ($action === 'approve') {
                $saleModel->approveDelete($saleId);
            } elseif ($action === 'reject') {
                $saleModel->rejectDelete($saleId);
            }
            
            header('Location: ' . BASE_URL . '/sales');
            exit;
        }
    }

    public function returns() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $saleId = $_POST['sale_id'];
            $returnedItems = $_POST['returns']; // Array of item_id => quantity
            
            $pdo = Database::getInstance();
            $saleModel = new Sale($pdo);
            
            try {
                $saleModel->processReturn($saleId, $returnedItems, $_SESSION['user_id']);
                header('Location: ' . BASE_URL . '/sales/view?id=' . $saleId);
            } catch (Exception $e) {
                // For now, redirect back with error in session if we had a flash message system.
                // Since we don't, we'll just redirect back.
                header('Location: ' . BASE_URL . '/sales/view?id=' . $saleId);
            }
            exit;
        }
    }
}

