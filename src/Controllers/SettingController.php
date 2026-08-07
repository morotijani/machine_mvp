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
                'company_email' => $_POST['company_email'],
                'receipt_type' => $_POST['receipt_type'] ?? 'a4',
                'enable_debt_module' => isset($_POST['enable_debt_module']) ? 1 : 0,
                'enable_barcode_reader' => isset($_POST['enable_barcode_reader']) ? 1 : 0,
                'enable_desktop_setup' => isset($_POST['enable_desktop_setup']) ? 1 : 0,
                'cloud_url' => rtrim($_POST['cloud_url'] ?? '', '/'),
                'sync_api_key' => $_POST['sync_api_key'] ?? ''
            ];

            // Handle Logo Upload
            if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
                
                // Delete old logo
                if (!empty($currentSettings['company_logo'])) {
                    $oldPath = dirname(__DIR__, 2) . '/public/' . ltrim($currentSettings['company_logo'], '/');
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/settings/';
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

    public function cleanLogs() {
        AuthMiddleware::requireLogin();
        AuthMiddleware::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $period = $_POST['clean_period'] ?? '';
            $pdo = Database::getInstance();
            $dateConstraint = "";
            $params = [];

            if ($period === 'last_week') {
                $dateConstraint = "created_at < :date";
                $params['date'] = date('Y-m-d H:i:s', strtotime('-7 days'));
            } elseif ($period === 'last_month') {
                $dateConstraint = "created_at < :date";
                $params['date'] = date('Y-m-d H:i:s', strtotime('-30 days'));
            } elseif ($period === 'last_2months') {
                $dateConstraint = "created_at < :date";
                $params['date'] = date('Y-m-d H:i:s', strtotime('-60 days'));
            } elseif ($period === 'all') {
                $dateConstraint = "1=1";
            }

            if ($dateConstraint !== "") {
                try {
                    $pdo->beginTransaction();
                    
                    // Clean item_logs
                    $stmt1 = $pdo->prepare("DELETE FROM item_logs WHERE $dateConstraint");
                    $stmt1->execute($params);
                    $itemLogsDeleted = $stmt1->rowCount();
                    
                    // Clean user_logins
                    $loginDateConstraint = str_replace('created_at', 'login_at', $dateConstraint);
                    $stmt2 = $pdo->prepare("DELETE FROM user_logins WHERE $loginDateConstraint");
                    $stmt2->execute($params);
                    $userLoginsDeleted = $stmt2->rowCount();
                    
                    $pdo->commit();
                    $success = "Successfully deleted $itemLogsDeleted item logs and $userLoginsDeleted user logins.";
                } catch (\Exception $e) {
                    $pdo->rollBack();
                    $error = "Failed to clean logs: " . $e->getMessage();
                }
            } else {
                $error = "Invalid cleaning period selected.";
            }

            $settingModel = new Setting($pdo);
            $settings = $settingModel->get();
            require __DIR__ . '/../../views/settings/index.php';
        }
    }
}
