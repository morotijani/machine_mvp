<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\User;

class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $pdo = Database::getInstance();
            $userModel = new User($pdo);
            $user = $userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['profile_image'] = $user['profile_image'];
                $_SESSION['role'] = $user['role'];
                
                header('Location: ' . BASE_URL . '/sales/create');
                exit;
            } else {
                $error = "Invalid username or password";
                require __DIR__ . '/../../views/auth/login.php';
            }
        } else {
            require __DIR__ . '/../../views/auth/login.php';
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
