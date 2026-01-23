<?php
namespace App\Controllers;

use App\Config\Database;
use App\Middleware\AuthMiddleware;
use PDO;

class UserController {
    
    public function index() {
        AuthMiddleware::requireAdmin();
        $pdo = Database::getInstance();
        $userModel = new \App\Models\User($pdo);
        $users = $userModel->getAll();
        require __DIR__ . '/../../views/users/index.php';
    }

    public function create() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];
            $fullname = $_POST['fullname'] ?? null;
            
            // Handle Profile Image Upload
            $profileImage = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileTmpPath = $_FILES['profile_image']['tmp_name'];
                $fileName = $_FILES['profile_image']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = uniqid('user_') . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $profileImage = 'uploads/profiles/' . $newFileName;
                    }
                }
            }
            
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, fullname, profile_image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $password, $role, $fullname, $profileImage]);
            
            header('Location: ' . BASE_URL . '/users');
            exit;
        }
        require __DIR__ . '/../../views/users/create.php';
    }
    public function toggleStatus() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'];
            $status = $_POST['status']; // 1 for active, 0 for inactive
            
            // Prevent disabling oneself
            if ($userId == $_SESSION['user_id']) {
                 // You might want to handle this error more gracefully or just ignore
                 header('Location: ' . BASE_URL . '/users');
                 exit;
            }

            $pdo = Database::getInstance();
            $userModel = new \App\Models\User($pdo);
            $userModel->updateStatus($userId, $status);
            
            header('Location: ' . BASE_URL . '/users');
            exit;
        }
    }
    
    public function updateRole() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'];
            $role = $_POST['role'];
            
            // Prevent changing own role
            if ($userId == $_SESSION['user_id']) {
                header('Location: ' . BASE_URL . '/users');
                exit;
            }

            // Validate role
            if (!in_array($role, ['admin', 'sales'])) {
                header('Location: ' . BASE_URL . '/users');
                exit;
            }

            $pdo = Database::getInstance();
            $userModel = new \App\Models\User($pdo);
            $userModel->updateRole($userId, $role);
            
            header('Location: ' . BASE_URL . '/users');
            exit;
        }
    }

    public function delete() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'];
            
            // Prevent deleting oneself
            if ($userId == $_SESSION['user_id']) {
                 header('Location: ' . BASE_URL . '/users');
                 exit;
            }

            $pdo = Database::getInstance();
            $userModel = new \App\Models\User($pdo);
            $userModel->delete($userId);
            
            header('Location: ' . BASE_URL . '/users');
            exit;
        }
    }
}
