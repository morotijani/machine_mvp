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

        // Redirect to Bundle Edit if it is a bundle
        if ($item['type'] === 'bundle') {
            $components = $itemModel->getBundleComponents($id);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Handle Bundle Edit (Metadata only for now)
                $data = [
                    'name' => $_POST['name'],
                    'category' => $_POST['category'],
                    'sku' => $_POST['sku'] ?: null,
                    'price' => $_POST['price'],
                    'location' => $_POST['location'],
                ];
                
                // Keep image logic shared or repeat? Repeat for safety in block.
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                     // Delete old
                     if (!empty($item['image_path']) && file_exists(__DIR__ . '/../../public/' . $item['image_path'])) {
                        unlink(__DIR__ . '/../../public/' . $item['image_path']);
                    }
                    $uploadDir = __DIR__ . '/../../public/uploads/items/';
                    $filename = uniqid('item_') . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
                    $data['image_path'] = 'uploads/items/' . $filename;
                } else {
                    $data['image_path'] = $item['image_path'];
                }
                
                // We use the same update method because it handles the fields we are changing
                // But we must NOT change the quantity/stock here as that breaks the bundle logic.
                // The update() method in Item.php updates quantity. We need to be careful.
                // Item::update updates 'quantity'. If we pass it, it blindly updates.
                // We should probably NOT pass quantity for bundle edit, OR pass the existing quantity.
                // Passing existing quantity is safer to avoid drift.
                $data['quantity'] = $item['quantity'];
                
                // Wait, Item::update expects 'unit'.
                $data['unit'] = 'bundle';
                $data['cost_price'] = $item['cost_price']; // Keep cost price same for now or allow update?
                
                $itemModel->update($id, $data);
                header('Location: ' . BASE_URL . '/items');
                exit;
            }

            require __DIR__ . '/../../views/items/edit_bundle.php';
            return;
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

    public function createBundle() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();
        $itemModel = new Item($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'name' => $_POST['name'],
                    'category' => $_POST['category'],
                    'sku' => $_POST['sku'],
                    'price' => $_POST['price'],
                    'quantity' => $_POST['quantity'],
                    'location' => $_POST['location'],
                    'unit' => 'bundle'
                ];
                
                // Image Upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../public/uploads/items/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    
                    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName);
                    $data['image_path'] = 'uploads/items/' . $fileName;
                }

                // Components
                $components = [];
                if (isset($_POST['items']) && is_array($_POST['items'])) {
                    foreach ($_POST['items'] as $index => $itemId) {
                        if (!empty($itemId) && !empty($_POST['quantities'][$index])) {
                            $components[] = [
                                'id' => $itemId,
                                'quantity' => $_POST['quantities'][$index]
                            ];
                        }
                    }
                }

                if (empty($components)) {
                    throw new \Exception("A bundle must have at least one item.");
                }

                $itemModel->createBundle($data, $components);
                header('Location: ' . BASE_URL . '/items');
                exit;

            } catch (\Exception $e) {
                $error = $e->getMessage();
                $items = $itemModel->getAll();
                require __DIR__ . '/../../views/items/create_bundle.php';
                return;
            }
        }

        $items = $itemModel->getAll();
        require __DIR__ . '/../../views/items/create_bundle.php';
    }

    public function ungroupBundle() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bundleId = $_POST['bundle_id'];
            $quantity = (int)$_POST['quantity'];
            
            if ($quantity < 1) {
                header('Location: ' . BASE_URL . '/items/edit?id=' . $bundleId . '&error=Invalid quantity');
                exit;
            }

            $pdo = Database::getInstance();
            $itemModel = new Item($pdo);
            
            try {
                $itemModel->disassembleBundle($bundleId, $quantity);
                header('Location: ' . BASE_URL . '/items/edit?id=' . $bundleId . '&success=Ungrouped successfully');
            } catch (\Exception $e) {
                header('Location: ' . BASE_URL . '/items/edit?id=' . $bundleId . '&error=' . urlencode($e->getMessage()));
            }
            exit;
        }
    }
}
