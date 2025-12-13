<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Customer;
use App\Middleware\AuthMiddleware;

class CustomerController {
    
    public function index() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();
        $customerModel = new Customer($pdo);
        $customers = $customerModel->getWithDebt(); // Show with debt by default as it's useful
        require __DIR__ . '/../../views/customers/index.php';
    }

    public function create() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';

            $pdo = Database::getInstance();
            $customerModel = new Customer($pdo);
            $customerModel->create($name, $phone, $address);
            
            header('Location: ' . BASE_URL . '/customers');
            exit;
        }
        // If GET, it's usually via a modal or inline form, but let's support a standalone page if needed
        // For now, redirect to index
        header('Location: ' . BASE_URL . '/customers'); 
    }
    
    // API endpoint for searching customers in sales view
    public function apiSearch() {
        AuthMiddleware::requireLogin(); 
        $query = $_GET['q'] ?? '';
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT id, name, phone FROM customers WHERE name LIKE :q OR phone LIKE :q LIMIT 10");
        $stmt->execute(['q' => "%$query%"]);
        echo json_encode($stmt->fetchAll());
        exit;
    }
    public function edit() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';

            $pdo = Database::getInstance();
            $customerModel = new Customer($pdo);
            $customerModel->update($id, $name, $phone, $address);
            
            header('Location: ' . BASE_URL . '/customers');
            exit;
        }
        header('Location: ' . BASE_URL . '/customers');
    }
}
