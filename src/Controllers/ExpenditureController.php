<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Expenditure;
use App\Middleware\AuthMiddleware;

class ExpenditureController {
    public function index() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();
        $model = new Expenditure($pdo);

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $userId = ($_SESSION['role'] === 'admin') ? null : $_SESSION['user_id'];
        $expenditures = $model->getAll($limit, $offset, $search, $userId);
        $totalItems = $model->countAll($search, $userId);
        $totalPages = ceil($totalItems / $limit);

        require __DIR__ . '/../../views/expenditures/index.php';
    }

    public function create() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category' => $_POST['category'],
                'amount' => $_POST['amount'],
                'description' => $_POST['description'],
                'date' => $_POST['date'],
                'recorded_by' => $_SESSION['user_id']
            ];

            $pdo = Database::getInstance();
            $model = new Expenditure($pdo);
            $model->create($data);

            header('Location: ' . BASE_URL . '/expenditures?success=Expenditure added successfully');
            exit;
        }
        require __DIR__ . '/../../views/expenditures/create.php';
    }

    public function edit() {
        AuthMiddleware::requireLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/expenditures');
            exit;
        }

        $pdo = Database::getInstance();
        $model = new Expenditure($pdo);
        $expenditure = $model->find($id);

        if (!$expenditure) {
            header('Location: ' . BASE_URL . '/expenditures');
            exit;
        }

        // Prevent sales users from editing others' expenditures
        if ($_SESSION['role'] !== 'admin' && $expenditure['recorded_by'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/expenditures?error=Access denied');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category' => $_POST['category'],
                'amount' => $_POST['amount'],
                'description' => $_POST['description'],
                'date' => $_POST['date']
            ];

            $model->update($id, $data);
            header('Location: ' . BASE_URL . '/expenditures?success=Expenditure updated successfully');
            exit;
        }

        require __DIR__ . '/../../views/expenditures/edit.php';
    }

    public function delete() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $pdo = Database::getInstance();
            $model = new Expenditure($pdo);
            $expenditure = $model->find($id);

            if (!$expenditure) {
                header('Location: ' . BASE_URL . '/expenditures');
                exit;
            }

            // Prevent sales users from deleting others' expenditures
            if ($_SESSION['role'] !== 'admin' && $expenditure['recorded_by'] != $_SESSION['user_id']) {
                header('Location: ' . BASE_URL . '/expenditures?error=Access denied');
                exit;
            }

            $model->delete($id);

            header('Location: ' . BASE_URL . '/expenditures?success=Expenditure deleted successfully');
            exit;
        }
    }
}
