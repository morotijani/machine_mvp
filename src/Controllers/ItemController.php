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
                'image_path' => null,
            ];

            // Handle Image Upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/items/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('item_') . '.' . $extension;
                $targetFile = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $data['image_path'] = 'uploads/items/' . $filename;
                }
            }

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

             // Handle Image Upload
            // Handle Image Upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // Delete old image if exists
                if (!empty($item['image_path'])) {
                    $oldImagePath = __DIR__ . '/../../public/' . $item['image_path'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $uploadDir = __DIR__ . '/../../public/uploads/items/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('item_') . '.' . $extension;
                $targetFile = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $data['image_path'] = 'uploads/items/' . $filename;
                }
            } else {
                 // Keep existing image if not uploading new one
                 $data['image_path'] = $item['image_path'] ?? null;
                 // Note: Ideally the model update method should be smart enough, or we pass what's there.
                 // The Model update() query binds all params. If we don't pass image_path, it might fail or set null if not careful.
                 // Let's check the Model update method. It does NOT have image_path in query yet.
            }

            $itemModel->update($id, $data);
            header('Location: ' . BASE_URL . '/items');
            exit;
        }

        require __DIR__ . '/../../views/items/edit.php';
    }
}
