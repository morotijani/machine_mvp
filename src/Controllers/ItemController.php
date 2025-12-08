<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Item;
use App\Middleware\AuthMiddleware;

class ItemController {
    
    public function index() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();
        $itemModel = new Item($pdo);
        $items = $itemModel->getAll();
        require __DIR__ . '/../../views/items/index.php';
    }

    public function create() {
        AuthMiddleware::requireLogin(); // Maybe Admin only? Requirement says 'Admin create items'
        AuthMiddleware::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'category' => $_POST['category'],
                'sku' => $_POST['sku'] ?: null,
                'unit' => $_POST['unit'] ?: 'pcs',
                'price' => $_POST['price'],
                'cost_price' => $_POST['cost_price'],
                'quantity' => $_POST['quantity'],
                'location' => $_POST['location'],
            ];

            $pdo = Database::getInstance();
            $itemModel = new Item($pdo);
            $itemModel->create($data);
            header('Location: ' . BASE_URL . '/items');
            exit;
        }

        require __DIR__ . '/../../views/items/create.php';
    }

    public function edit() {
        AuthMiddleware::requireAdmin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/items');
            exit;
        }

        $pdo = Database::getInstance();
        $itemModel = new Item($pdo);
        $item = $itemModel->find($id);

        if (!$item) {
            header('Location: ' . BASE_URL . '/items');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'category' => $_POST['category'],
                'sku' => $_POST['sku'] ?: null,
                'unit' => $_POST['unit'] ?: 'pcs',
                'price' => $_POST['price'],
                'cost_price' => $_POST['cost_price'],
                'quantity' => $_POST['quantity'],
                'location' => $_POST['location'],
            ];
            $itemModel->update($id, $data);
            header('Location: ' . BASE_URL . '/items');
            exit;
        }

        require __DIR__ . '/../../views/items/edit.php';
    }
}
