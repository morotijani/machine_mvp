<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Setting;
use App\Middleware\AuthMiddleware;

class SettingController {
    
    public function index() {
        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();
        
        $pdo = Database::getInstance();
        $settingModel = new Setting($pdo);
        $settings = $settingModel->get();
        
        require __DIR__ . '/../../views/settings/index.php';
    }

    public function update() {
        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = Database::getInstance();
            $settingModel = new Setting($pdo);
            $currentSettings = $settingModel->get();

            $data = [
                'id' => $currentSettings['id'],
                'company_name' => $_POST['company_name'],
                'company_address' => $_POST['company_address'],
                'company_phone' => $_POST['company_phone'],
                'company_email' => $_POST['company_email']
            ];

            // Handle Logo Upload
            if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
                
                // Delete old logo
                if (!empty($currentSettings['company_logo'])) {
                    $oldPath = __DIR__ . '/../../public/' . $currentSettings['company_logo'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $uploadDir = __DIR__ . '/../../public/uploads/settings/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $extension = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
                $filename = 'logo_' . uniqid() . '.' . $extension;
                $targetFile = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $targetFile)) {
                    $data['company_logo'] = 'uploads/settings/' . $filename;
                }
            }

            if ($settingModel->update($data)) {
                $success = "Settings updated successfully.";
            } else {
                $error = "Failed to update settings.";
            }
            
            // Re-fetch for view
            $settings = $settingModel->get();
            require __DIR__ . '/../../views/settings/index.php';
        }
    }
}
