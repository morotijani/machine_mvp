<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Proforma;
use App\Models\Setting;
use App\Middleware\AuthMiddleware;

class ProformaController {
    public function index() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();
        $proformaModel = new Proforma($pdo);
        $proformas = $proformaModel->getAll();

        require __DIR__ . '/../../views/proforma/index.php';
    }

    public function create() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();
        $itemModel = new Item($pdo);
        $customerModel = new Customer($pdo);
        
        $items = $itemModel->getAll();
        $customers = $customerModel->getAll();

        require __DIR__ . '/../../views/proforma/create.php';
    }

    public function store() {
        AuthMiddleware::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || empty($data['items'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
                return;
            }

            $pdo = Database::getInstance();
            $proformaModel = new Proforma($pdo);

            $proformaData = [
                'customer_id' => !empty($data['customer_id']) ? $data['customer_id'] : null,
                'total_amount' => $data['total'],
                'recorded_by' => $_SESSION['user_id'],
                'notes' => $data['notes'] ?? null
            ];

            try {
                $proformaId = $proformaModel->create($proformaData, $data['items']);
                echo json_encode([
                    'success' => true,
                    'redirect' => BASE_URL . '/proformas/preview?id=' . $proformaId
                ]);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    public function preview() {
        AuthMiddleware::requireLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/proformas');
            exit;
        }

        $pdo = Database::getInstance();
        $proformaModel = new Proforma($pdo);
        
        $proforma = $proformaModel->find($id);
        if (!$proforma) {
            header('Location: ' . BASE_URL . '/proformas');
            exit;
        }

        $items = $proformaModel->getItems($id);

        // Fetch bundle components for any bundle items in this proforma
        $bundleComponents = [];
        foreach ($items as $item) {
            if ($item['item_type'] === 'bundle') {
                $bundleComponents[$item['item_id']] = $proformaModel->getBundleComponents($item['item_id']);
            }
        }

        $settingModel = new Setting($pdo);
        $settings = $settingModel->get();

        require __DIR__ . '/../../views/proforma/preview.php';
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/proformas');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = "Invalid Pro Forma ID.";
            header('Location: ' . BASE_URL . '/proformas');
            exit;
        }

        $pdo = Database::getInstance();
        $proformaModel = new \App\Models\Proforma($pdo);
        
        if ($proformaModel->delete($id)) {
            $_SESSION['success'] = "Pro Forma Invoice deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete Pro Forma Invoice.";
        }

        header('Location: ' . BASE_URL . '/proformas');
        exit;
    }
}
