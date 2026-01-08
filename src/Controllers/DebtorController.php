<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Debtor;
use App\Middleware\AuthMiddleware;

class DebtorController {
    public function index() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();
        $model = new Debtor($pdo);

        $search = $_GET['search'] ?? null;
        $debtors = $model->getAll($search);

        require __DIR__ . '/../../views/debtors/index.php';
    }

    public function create() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'phone' => $_POST['phone'],
                'total_amount' => $_POST['total_amount'],
                'description' => $_POST['description']
            ];

            $pdo = Database::getInstance();
            $model = new Debtor($pdo);
            $model->create($data);

            header('Location: ' . BASE_URL . '/debtors?success=Debtor added successfully');
            exit;
        }
        require __DIR__ . '/../../views/debtors/create.php';
    }

    public function recordPayment() {
        AuthMiddleware::requireLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/debtors');
            exit;
        }

        $pdo = Database::getInstance();
        $model = new Debtor($pdo);
        $debtor = $model->find($id);

        if (!$debtor) {
            header('Location: ' . BASE_URL . '/debtors');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = $_POST['amount'];
            $date = $_POST['payment_date'];
            $uid = $_SESSION['user_id'];

            $model->recordPayment($id, $amount, $date, $uid);
            header('Location: ' . BASE_URL . '/debtors/history?id=' . $id . '&success=Payment recorded successfully');
            exit;
        }

        require __DIR__ . '/../../views/debtors/payment.php';
    }

    public function history() {
        AuthMiddleware::requireLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/debtors');
            exit;
        }

        $pdo = Database::getInstance();
        $model = new Debtor($pdo);
        $debtor = $model->find($id);
        
        if (!$debtor) {
            header('Location: ' . BASE_URL . '/debtors');
            exit;
        }

        $history = $model->getHistory($id);
        require __DIR__ . '/../../views/debtors/history.php';
    }

    public function delete() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $pdo = Database::getInstance();
            $model = new Debtor($pdo);
            $model->softDelete($id);

            header('Location: ' . BASE_URL . '/debtors?success=Debtor removed');
            exit;
        }
    }
}
