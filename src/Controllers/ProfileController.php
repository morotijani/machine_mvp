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
            $pdo = Database::getInstance();
            $userModel = new User($pdo);
            $user = $userModel->find($userId);

            // 1. Profile Update
            if (isset($_POST['fullname'])) {
                $fullname = $_POST['fullname'];
                $profileImage = $user['profile_image']; // Default to existing

                // Handle Image Upload
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

                if ($userModel->updateProfile($userId, $fullname, $profileImage)) {
                    // Update Session
                    $_SESSION['fullname'] = $fullname;
                    $_SESSION['profile_image'] = $profileImage;
                    $success = "Profile updated successfully.";
                } else {
                    $error = "Failed to update profile.";
                }
            }

            // 2. Password Update (if fields are filled)
            if (!empty($_POST['new_password'])) {
                $newPassword = $_POST['new_password'];
                $confirmPassword = $_POST['confirm_password'];
                
                if ($newPassword !== $confirmPassword) {
                    $error = "Passwords do not match.";
                } else {
                    if ($userModel->updatePassword($userId, $newPassword)) {
                        $success = "Password updated successfully.";
                    } else {
                        $error = "Failed to update password.";
                    }
                }
            }

            $this->renderView($userId, $error ?? null, $success ?? null);
        }
    }
    
    private function renderView($userId, $error = null, $success = null) {
        $pdo = Database::getInstance();
        $userModel = new User($pdo);
        $user = $userModel->find($userId);
        require __DIR__ . '/../../views/profile/index.php';
    }
}
