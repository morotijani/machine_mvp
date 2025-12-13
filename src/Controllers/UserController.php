<?php
namespace App\Controllers;

use App\Config\Database;
use App\Middleware\AuthMiddleware;
use PDO;

class UserController {
    
    public function index() {
        AuthMiddleware::requireAdmin();
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll();
        require __DIR__ . '/../../views/users/index.php';
    }

    public function create() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];
            
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $password, $role]);
            
            header('Location: ' . BASE_URL . '/users');
            exit;
        }
        require __DIR__ . '/../../views/users/create.php';
    }
}
