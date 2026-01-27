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
                if ($user['is_active'] == 0) {
                   $error = "Your account has been disabled. Please contact the administrator.";
                   $pdo = Database::getInstance();
                   $settingModel = new \App\Models\Setting($pdo);
                   $settings = $settingModel->get();
                   require __DIR__ . '/../../views/auth/login.php';
                   return;
                }
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['profile_image'] = $user['profile_image'];
                $_SESSION['role'] = $user['role'];
                
                // Record Login Activity
                $stmtLog = $pdo->prepare("INSERT INTO user_logins (user_id, ip_address, user_agent) VALUES (?, ?, ?)");
                $stmtLog->execute([
                    $user['id'],
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
                
                header('Location: ' . BASE_URL . '/sales/create');
                exit;
            } else {
                $error = "Invalid username or password";
                $pdo = Database::getInstance();
                $settingModel = new \App\Models\Setting($pdo);
                $settings = $settingModel->get();
                require __DIR__ . '/../../views/auth/login.php';
            }
        } else {
            $pdo = Database::getInstance();
            $settingModel = new \App\Models\Setting($pdo);
            $settings = $settingModel->get();
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
