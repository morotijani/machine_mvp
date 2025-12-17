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
                'sku' => !empty($_POST['sku']) ? $_POST['sku'] : strtoupper(substr(uniqid('SKU'), 0, 10)),
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
            $allItems = $itemModel->getAll(); // Need all items for the select list in edit view
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $data = [
                        'name' => $_POST['name'],
                        'category' => $_POST['category'],
                        'sku' => !empty($_POST['sku']) ? $_POST['sku'] : $item['sku'],
                        'price' => $_POST['price'],
                        'location' => $_POST['location'],
                        'quantity' => $_POST['quantity'], // New Bundle Quantity
                        'unit' => 'bundle'
                    ];
                    
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
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

                    // Process Components
                    $newComponents = [];
                    if (isset($_POST['items']) && is_array($_POST['items'])) {
                        foreach ($_POST['items'] as $index => $itemId) {
                            $qty = (int)$_POST['quantities'][$index];
                            if (!empty($itemId) && $qty > 0) {
                                $newComponents[] = [
                                    'id' => $itemId,
                                    'quantity' => $qty
                                ];
                            } elseif ($qty <= 0) {
                                throw new \Exception("Component '$itemId' quantity is invalid ($qty). Must be greater than 0.");
                            }
                        }
                    }

                    if ((int)$data['quantity'] < 0) {
                         throw new \Exception("Bundle quantity cannot be negative.");
                    }

                    if (empty($newComponents)) {
                        throw new \Exception("A bundle must have at least one item.");
                    }
                    
                    $itemModel->updateBundle($id, $data, $newComponents);
                    header('Location: ' . BASE_URL . '/items');
                    exit;

                } catch (\Exception $e) {
                    $error = $e->getMessage();
                    // Fallthrough to load view with error
                }
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
                    'sku' => !empty($_POST['sku']) ? $_POST['sku'] : strtoupper(substr(uniqid('BND'), 0, 10)),
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
                header('Location: ' . BASE_URL . '/items?success=Ungrouped successfully');
            } catch (\Exception $e) {
                header('Location: ' . BASE_URL . '/items/edit?id=' . $bundleId . '&error=' . urlencode($e->getMessage()));
            }
            exit;
        }
    }
}
