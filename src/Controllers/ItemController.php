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
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;
        $lowStock = isset($_GET['low_stock']) && $_GET['low_stock'] == 1;
        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'DESC';
        
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $items = $itemModel->getAll($limit, $offset, $search, $lowStock, $sort, $order);
        $totalItems = $itemModel->countAll($search, $lowStock);
        $totalPages = ceil($totalItems / $limit);
        
        require __DIR__ . '/../../views/items/index.php';
    }

    public function create() {
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

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/items/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($_FILES['image']['tmp_name']);
                
                if (in_array($mimeType, $allowedMimeTypes)) {
                    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('item_') . '.' . $extension;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                        $data['image_path'] = 'uploads/items/' . $filename;
                    }
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

        if ($item['type'] === 'bundle') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $data = [
                        'name' => $_POST['name'],
                        'category' => $_POST['category'],
                        'sku' => !empty($_POST['sku']) ? $_POST['sku'] : $item['sku'],
                        'price' => $_POST['price'],
                        'location' => $_POST['location'],
                        'quantity' => $_POST['quantity'],
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

                    $newComponents = [];
                    if (isset($_POST['items']) && is_array($_POST['items'])) {
                        foreach ($_POST['items'] as $index => $itemId) {
                            $qty = (int)$_POST['quantities'][$index];
                            if (!empty($itemId) && $qty > 0) {
                                $newComponents[] = ['id' => $itemId, 'quantity' => $qty];
                            }
                        }
                    }

                    if ((int)$data['quantity'] <= 0) throw new \Exception("Bundle quantity cannot be 0.");
                    if (empty($newComponents)) throw new \Exception("A bundle must have at least one item.");
                    
                    $itemModel->updateBundle($id, $data, $newComponents);
                    header('Location: ' . BASE_URL . '/items');
                    exit;

                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            }
            $components = $itemModel->getBundleComponents($id);
            $allItems = $itemModel->getAll();
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

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                if (!empty($item['image_path'])) {
                    $old = __DIR__ . '/../../public/' . $item['image_path'];
                    if (file_exists($old)) unlink($old);
                }
                $uploadDir = __DIR__ . '/../../public/uploads/items/';
                $filename = uniqid('item_') . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                    $data['image_path'] = 'uploads/items/' . $filename;
                }
            } else {
                $data['image_path'] = $item['image_path'];
            }

            $itemModel->update($id, $data);
            $itemModel->updateParentBundlePrices($id);
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
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../public/uploads/items/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName);
                    $data['image_path'] = 'uploads/items/' . $fileName;
                }

                $components = [];
                if (isset($_POST['items']) && is_array($_POST['items'])) {
                    foreach ($_POST['items'] as $index => $itemId) {
                        if (!empty($itemId) && !empty($_POST['quantities'][$index])) {
                            $components[] = ['id' => $itemId, 'quantity' => $_POST['quantities'][$index]];
                        }
                    }
                }

                if (empty($components)) throw new \Exception("A bundle must have at least one item.");

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

        $duplicateItem = null;
        $duplicateComponents = [];
        if (isset($_GET['duplicate_from'])) {
            $duplicateId = (int)$_GET['duplicate_from'];
            $duplicateItem = $itemModel->find($duplicateId);
            if ($duplicateItem && $duplicateItem['type'] === 'bundle') {
                $duplicateComponents = $itemModel->getBundleComponents($duplicateId);
            }
        }

        $items = $itemModel->getAll();
        require __DIR__ . '/../../views/items/create_bundle.php';
    }

    public function preview() {
        AuthMiddleware::requireLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . BASE_URL . '/items'); exit; }

        $pdo = Database::getInstance();
        $itemModel = new Item($pdo);
        $item = $itemModel->find($id);
        
        if (!$item || $item['type'] !== 'bundle') { header('Location: ' . BASE_URL . '/items'); exit; }

        $components = $itemModel->getBundleComponents($id);
        foreach ($components as &$comp) {
            $stmt = $pdo->prepare("SELECT price FROM items WHERE id = :id");
            $stmt->execute(['id' => $comp['child_item_id']]);
            $comp['selling_price'] = $stmt->fetchColumn();
        }
        unset($comp);

        require __DIR__ . '/../../views/items/preview.php';
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

    public function delete() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $pdo = Database::getInstance();
            
            // Check if item is part of a bundle
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM item_bundles WHERE child_item_id = :id");
            $stmt->execute(['id' => $id]);
            if ($stmt->fetchColumn() > 0) {
                header('Location: ' . BASE_URL . '/items?error=Cannot delete item. It is a component of one or more bundles.');
                exit;
            }

            $itemModel = new Item($pdo);
            $itemModel->delete($id);
            header('Location: ' . BASE_URL . '/items?success=Item removed');
            exit;
        }
    }
}
