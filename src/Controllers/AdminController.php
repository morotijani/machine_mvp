<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\User;
use App\Models\Item;
use App\Models\Sale;
use App\Middleware\AuthMiddleware;

class AdminController {
    
    public function trash() {
        AuthMiddleware::requireAdmin();
        $pdo = Database::getInstance();
        
        $itemModel = new Item($pdo);
        $userModel = new User($pdo);
        $saleModel = new Sale($pdo);
        
        $deletedItems = $itemModel->getDeleted();
        $deletedUsers = $userModel->getDeleted();
        $deletedSales = $saleModel->getDeleted();
        
        $expModel = new \App\Models\Expenditure($pdo);
        $deletedExpenditures = $expModel->getDeleted();
        
        require __DIR__ . '/../../views/admin/trash.php';
    }

    public function restore() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'];
            $id = $_POST['id'];
            $pdo = Database::getInstance();
            
            try {
                if ($type === 'item') {
                    $model = new Item($pdo);
                    $model->restore($id);
                } elseif ($type === 'user') {
                    $model = new User($pdo);
                    $model->restore($id);
                } elseif ($type === 'sale') {
                    $model = new Sale($pdo);
                    $model->restore($id);
                } elseif ($type === 'expenditure') {
                    $model = new \App\Models\Expenditure($pdo);
                    $model->restore($id);
                }
                header('Location: ' . BASE_URL . '/admin/trash?success=Restored successfully');
            } catch (\Exception $e) {
                header('Location: ' . BASE_URL . '/admin/trash?error=' . urlencode($e->getMessage()));
            }
            exit;
        }
    }

    public function deleteForever() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'];
            $id = $_POST['id'];
            $pdo = Database::getInstance();
            
            try {
                if ($type === 'item') {
                    $model = new Item($pdo);
                    $model->hardDelete($id);
                } elseif ($type === 'user') {
                    $model = new User($pdo);
                    $model->hardDelete($id);
                } elseif ($type === 'sale') {
                    $model = new Sale($pdo);
                    $model->hardDelete($id);
                } elseif ($type === 'expenditure') {
                    $model = new \App\Models\Expenditure($pdo);
                    $model->hardDelete($id);
                }
                header('Location: ' . BASE_URL . '/admin/trash?success=Deleted forever');
            } catch (\Exception $e) {
                header('Location: ' . BASE_URL . '/admin/trash?error=' . urlencode($e->getMessage()));
            }
            exit;
        }
    }
}
