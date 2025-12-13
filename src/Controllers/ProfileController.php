<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\User;
use App\Middleware\AuthMiddleware;

class ProfileController {
    
    public function index() {
        AuthMiddleware::requireLogin();
        $userId = $_SESSION['user_id'];
        
        $pdo = Database::getInstance();
        $userModel = new User($pdo);
        $user = $userModel->find($userId);
        
        if (!$user) {
            header('Location: ' . BASE_URL . '/logout');
            exit;
        }
        
        require __DIR__ . '/../../views/profile/index.php';
    }

    public function update() {
        AuthMiddleware::requireLogin();
        $userId = $_SESSION['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            if (empty($newPassword) || empty($confirmPassword)) {
                $error = "Password fields cannot be empty.";
                $this->renderView($userId, $error);
                return;
            }
            
            if ($newPassword !== $confirmPassword) {
                $error = "Passwords do not match.";
                $this->renderView($userId, $error);
                return;
            }
            
            $pdo = Database::getInstance();
            $userModel = new User($pdo);
            
            // Optional: Check current password? Implementation simpler without for now, but safer with.
            // Requirement didn't specify, but let's assume direct update is ok for now.
            
            if ($userModel->updatePassword($userId, $newPassword)) {
                $success = "Password updated successfully.";
                $this->renderView($userId, null, $success);
            } else {
                $error = "Failed to update password.";
                $this->renderView($userId, $error);
            }
        }
    }
    
    private function renderView($userId, $error = null, $success = null) {
        $pdo = Database::getInstance();
        $userModel = new User($pdo);
        $user = $userModel->find($userId);
        require __DIR__ . '/../../views/profile/index.php';
    }
}
