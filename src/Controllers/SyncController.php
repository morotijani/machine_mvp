<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;

class SyncController {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    public function index() {
        \App\Middleware\AuthMiddleware::requireLogin();
        \App\Middleware\AuthMiddleware::requireAdmin();
        
        $settingModel = new \App\Models\Setting($this->pdo);
        $settings = $settingModel->get();
        
        if (isset($settings['enable_desktop_setup']) && $settings['enable_desktop_setup'] == 0) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        
        // Fetch unsynced counts for dashboard
        $unsyncedCounts = [];
        $tablesToCheck = [
            'sales' => 'Sales',
            'items' => 'Items',
            'customers' => 'Customers',
            'payments' => 'Payments',
            'expenditures' => 'Expenditures',
            'sale_returns' => 'Returns',
            'debt_repayments' => 'Debt Repayments'
        ];
        
        $totalUnsynced = 0;
        foreach ($tablesToCheck as $table => $label) {
            try {
                $stmt = $this->pdo->query("SELECT COUNT(*) FROM `$table` WHERE sync_status = 0");
                $count = (int)$stmt->fetchColumn();
                if ($count > 0) {
                    $unsyncedCounts[$label] = $count;
                    $totalUnsynced += $count;
                }
            } catch (\Exception $e) {
                // Table might not exist yet if older DB version
            }
        }
        
        require __DIR__ . '/../../views/sync/index.php';
    }
    
    public function pushToCloud() {
        \App\Middleware\AuthMiddleware::requireLogin();
        \App\Middleware\AuthMiddleware::requireAdmin();
        
        // Sync operations can take a long time on slow networks or with large payloads
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        
        $settingModel = new \App\Models\Setting($this->pdo);
        $settings = $settingModel->get();
        
        if (empty($settings['cloud_url']) || empty($settings['sync_api_key'])) {
            $this->jsonResponse(false, 'Sync not configured.', 400);
        }
        
        $cloudUrl = rtrim($settings['cloud_url'], '/');
        $apiKey = $settings['sync_api_key'];
        
        $tables = ['settings', 'users', 'customers', 'items', 'sales', 'sale_items', 'payments', 'inventory_logs'];
        
        // Check if optional tables exist
        $optionalTables = [
            'debtors', 'expenditures', 'user_logins', 'sale_return_items', 
            'sale_returns', 'payment_requests', 'item_logs', 'item_bundles', 
            'debt_repayments', 'customer_debt_payments', 'coffer_transactions'
        ];
        
        foreach ($optionalTables as $optTable) {
            if ($this->pdo->query("SHOW TABLES LIKE '$optTable'")->rowCount() > 0) {
                $tables[] = $optTable;
            }
        }
        
        $payload = ['tables' => []];
        $syncedIds = [];
        
        foreach ($tables as $table) {
            // Fetch all unsynced records
            $stmt = $this->pdo->query("SELECT * FROM `$table` WHERE sync_status = 0");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) > 0) {
                $payload['tables'][$table] = $rows;
                $syncedIds[$table] = array_column($rows, 'id');
            }
        }
        
        if (empty($payload['tables'])) {
            $this->jsonResponse(true, 'Everything is up to date.');
        }
        
        // Push Database Payload via cURL
        $ch = curl_init($cloudUrl . '/api/sync/receive');
        
        // Compress and encode payload to bypass WAF and post_max_size limits
        $jsonPayload = json_encode($payload);
        $compressedPayload = base64_encode(gzencode($jsonPayload, 9));
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['sync_data' => $compressedPayload]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $apiKey,
            'X-Sync-Api-Key: ' . $apiKey,
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MachineMVP-SyncClient/1.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 0); // No timeout for large syncs
        
        // Ignore SSL verification for local dev or misconfigured cloud servers
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $res = json_decode($response, true);
        
        if ($httpCode === 200 && $res && $res['success']) {
            // Update local sync_status to 1 for pushed records
            $this->pdo->beginTransaction();
            foreach ($syncedIds as $table => $ids) {
                if (count($ids) > 0) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $stmt = $this->pdo->prepare("UPDATE `$table` SET sync_status = 1 WHERE id IN ($placeholders)");
                    $stmt->execute($ids);
                }
            }
            $this->pdo->commit();
            
            // Now push files (Images) - we find files in 'public/uploads'
            $this->pushFilesToCloud($cloudUrl, $apiKey);
            
            $this->jsonResponse(true, 'Data pushed successfully.');
        } else {
            $errorMessage = $res['message'] ?? ($response ? strip_tags(substr($response, 0, 100)) : 'Unknown network error.');
            $this->jsonResponse(false, 'Cloud Sync Failed: ' . $errorMessage . ' (HTTP ' . $httpCode . ')');
        }
    }
    
    private function pushFilesToCloud($cloudUrl, $apiKey) {
        $uploadsDir = realpath(__DIR__ . '/../../public/uploads');
        if (!$uploadsDir || !is_dir($uploadsDir)) return;
        
        $activeFiles = []; // Track active files to sync deletions
        
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($uploadsDir));
        foreach ($iterator as $file) {
            if ($file->isDir()) continue;
            
            $filePath = $file->getRealPath();
            $relativePath = str_replace($uploadsDir . DIRECTORY_SEPARATOR, '', $filePath);
            $relativePath = str_replace('\\', '/', $relativePath); // normalize path for the cloud
            $activeFiles[] = $relativePath;
            
            $mimeType = @mime_content_type($file->getPathname()) ?: 'application/octet-stream';
            $cfile = curl_file_create($file->getPathname(), $mimeType, $file->getBasename());
            
            $postData = [
                'file' => $cfile,
                'path' => $relativePath
            ];
            
            $ch = curl_init($cloudUrl . '/api/sync/files');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey,
                'X-Sync-Api-Key: ' . $apiKey,
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_USERAGENT, 'MachineMVP-SyncClient/1.0');
            curl_setopt($ch, CURLOPT_TIMEOUT, 0); // No timeout for large files
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            curl_exec($ch);
            curl_close($ch);
        }
        
        // Tell the cloud server to delete any files not in the activeFiles list
        if (!empty($activeFiles)) {
            $ch = curl_init($cloudUrl . '/api/sync/cleanupFiles');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['active_files' => json_encode($activeFiles)]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer ' . $apiKey,
                'X-Sync-Api-Key: ' . $apiKey,
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_USERAGENT, 'MachineMVP-SyncClient/1.0');
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            curl_close($ch);
        }
    }
    
    private function verifyApiKey() {
        $headers = getallheaders();
        
        // Fallback for shared hosting where Apache strips Authorization headers
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        $customHeader = $headers['X-Sync-Api-Key'] ?? $headers['x-sync-api-key'] ?? '';
        
        $settingModel = new \App\Models\Setting($this->pdo);
        $settings = $settingModel->get();
        $expectedKey = $settings['sync_api_key'] ?? '';
        
        if (empty($expectedKey)) {
            $this->jsonResponse(false, 'Cloud server has no sync API key configured.', 403);
        }
        
        $providedKey = str_replace('Bearer ', '', $authHeader);
        if (empty($providedKey)) {
            $providedKey = $customHeader;
        }
        
        if ($providedKey !== $expectedKey) {
            $this->jsonResponse(false, 'Unauthorized. Invalid API Key.', 401);
        }
    }
    
    public function receiveDatabase() {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        $this->verifyApiKey();
        
        // Handle both old raw JSON and new compressed form-data
        if (isset($_POST['sync_data'])) {
            $decoded = base64_decode($_POST['sync_data']);
            $jsonString = gzdecode($decoded);
            $data = json_decode($jsonString, true);
        } else {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
        }
        
        if (!$data || !isset($data['tables'])) {
            $this->jsonResponse(false, 'Invalid payload received.', 400);
        }
        
        $isSqlite = \App\Config\Database::getDriver() === 'sqlite';
        if ($isSqlite) {
            $this->pdo->exec('PRAGMA foreign_keys = OFF;');
        } else {
            $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');
        }
        $this->pdo->beginTransaction();
        try {
            // Process tables in order of foreign key constraints
            $tables = ['settings', 'users', 'customers', 'items', 'item_bundles', 'sales', 'sale_items', 'sale_returns', 'sale_return_items', 'payment_requests', 'inventory_logs', 'item_logs', 'debtors', 'debt_repayments', 'customer_debt_payments', 'expenditures', 'coffer_transactions', 'user_logins', 'payments'];
            
            foreach ($tables as $table) {
                if (isset($data['tables'][$table]) && is_array($data['tables'][$table])) {
                    $this->upsertTable($table, $data['tables'][$table]);
                }
            }
            
            $this->pdo->commit();
            if ($isSqlite) $this->pdo->exec('PRAGMA foreign_keys = ON;');
            else $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');
            $this->jsonResponse(true, 'Database synced successfully.');
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            if ($isSqlite) $this->pdo->exec('PRAGMA foreign_keys = ON;');
            else $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');
            $this->jsonResponse(false, 'Sync failed: ' . $e->getMessage(), 500);
        }
    }
    
    private function upsertTable($table, $rows) {
        if (empty($rows)) return;
        
        $columns = array_keys($rows[0]);
        // Remove columns that shouldn't be overridden if they cause issues, but for 1-1 sync we want exact match.
        // However, we MUST ensure we don't accidentally update `cloud_url` or `sync_api_key` on the cloud from the local, 
        // otherwise it might break things. Actually, local has the key, cloud has the key. It's fine.
        
        $colNames = implode(', ', array_map(function($c) { return "`$c`"; }, $columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        
        $updateStmts = [];
        foreach ($columns as $col) {
            if ($col !== 'id') {
                if (\App\Config\Database::getDriver() === 'sqlite') {
                    $updateStmts[] = "`$col` = excluded.`$col`";
                } else {
                    $updateStmts[] = "`$col` = VALUES(`$col`)";
                }
            }
        }
        $updateSql = implode(', ', $updateStmts);
        
        if (\App\Config\Database::getDriver() === 'sqlite') {
            $sql = "INSERT INTO `$table` ($colNames) VALUES ($placeholders) ON CONFLICT(id) DO UPDATE SET $updateSql";
        } else {
            $sql = "INSERT INTO `$table` ($colNames) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateSql";
        }
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($rows as $row) {
            $values = [];
            foreach ($columns as $col) {
                $values[] = $row[$col];
            }
            $stmt->execute($values);
        }
    }
    
    public function receiveFiles() {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        $this->verifyApiKey();
        
        if (!isset($_FILES['file'])) {
            $this->jsonResponse(false, 'No file uploaded.', 400);
        }
        
        $relativePath = $_POST['path'] ?? '';
        if (empty($relativePath)) {
            $this->jsonResponse(false, 'File path not specified.', 400);
        }
        
        // Prevent directory traversal attacks
        if (strpos($relativePath, '..') !== false) {
            $this->jsonResponse(false, 'Invalid path.', 400);
        }
        
        $targetDir = __DIR__ . '/../../public/uploads/';
        $targetFile = $targetDir . $relativePath;
        
        // Ensure directory exists
        $dir = dirname($targetFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            $this->jsonResponse(true, "File $relativePath synced successfully.");
        } else {
            $this->jsonResponse(false, 'Failed to save file.', 500);
        }
    }
    
    public function cleanupFiles() {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        $this->verifyApiKey();
        
        $activeFilesJson = $_POST['active_files'] ?? '[]';
        $activeFiles = json_decode($activeFilesJson, true);
        
        if (!is_array($activeFiles)) {
            $this->jsonResponse(false, 'Invalid active files list.', 400);
        }
        
        $targetDir = realpath(__DIR__ . '/../../public/uploads');
        if (!$targetDir || !is_dir($targetDir)) {
             $this->jsonResponse(true, 'No uploads directory to clean.');
        }
        
        $normalizedActiveFiles = [];
        foreach ($activeFiles as $file) {
            $normalizedActiveFiles[] = str_replace('\\', '/', $file);
        }
        
        $deletedCount = 0;
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($targetDir));
        foreach ($iterator as $file) {
            if ($file->isDir()) continue;
            
            $filePath = $file->getRealPath();
            $relativePath = str_replace($targetDir . DIRECTORY_SEPARATOR, '', $filePath);
            $relativePath = str_replace('\\', '/', $relativePath);
            
            if (!in_array($relativePath, $normalizedActiveFiles)) {
                @unlink($filePath);
                $deletedCount++;
            }
        }
        
        $this->jsonResponse(true, "Cleanup complete. Deleted $deletedCount old files.");
    }
    
    // ==========================================
    // CLOUD SERVER METHODS (API Pull Providers)
    // ==========================================
    public function exportDatabase() {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        $this->verifyApiKey();
        
        $tables = ['settings', 'users', 'customers', 'items', 'item_bundles', 'sales', 'sale_items', 'sale_returns', 'sale_return_items', 'payment_requests', 'inventory_logs', 'item_logs', 'debtors', 'debt_repayments', 'customer_debt_payments', 'expenditures', 'coffer_transactions', 'user_logins', 'payments'];
        
        $payload = ['tables' => []];
        
        $isFull = isset($_GET['full']) && $_GET['full'] == '1';
        foreach ($tables as $table) {
            // Check if table exists (for backward compatibility if missing)
            if ($this->pdo->query("SHOW TABLES LIKE '$table'")->rowCount() > 0) {
                if ($isFull) {
                    $stmt = $this->pdo->query("SELECT * FROM `$table`");
                } else {
                    // Fetch all records modified on cloud (sync_status = 0)
                    $stmt = $this->pdo->query("SELECT * FROM `$table` WHERE sync_status = 0");
                }
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($rows) > 0) {
                    $payload['tables'][$table] = $rows;
                }
            }
        }
        
        // Compress for transport
        $jsonString = json_encode($payload);
        $compressed = gzencode($jsonString, 9);
        $base64 = base64_encode($compressed);
        
        $this->jsonResponse(true, $base64);
    }
    
    public function confirmPull() {
        $this->verifyApiKey();
        
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data || !isset($data['syncedIds'])) {
            $this->jsonResponse(false, 'Invalid payload received.', 400);
        }
        
        $syncedIds = $data['syncedIds'];
        
        $this->pdo->beginTransaction();
        try {
            foreach ($syncedIds as $table => $ids) {
                if (count($ids) > 0) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $stmt = $this->pdo->prepare("UPDATE `$table` SET sync_status = 1 WHERE id IN ($placeholders)");
                    $stmt->execute($ids);
                }
            }
            $this->pdo->commit();
            $this->jsonResponse(true, 'Pull confirmed and cloud status updated.');
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            $this->jsonResponse(false, 'Database error during pull confirmation: ' . $e->getMessage(), 500);
        }
    }
    
    // ==========================================
    // LOCAL CLIENT METHODS (Pull Requestor)
    // ==========================================
    public function pullFromCloud() {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        
        $settingModel = new \App\Models\Setting($this->pdo);
        $settings = $settingModel->get();
        
        if (empty($settings['cloud_url']) || empty($settings['sync_api_key'])) {
            $this->jsonResponse(false, 'Sync not configured.', 400);
        }
        
        $cloudUrl = rtrim($settings['cloud_url'], '/');
        $apiKey = $settings['sync_api_key'];
        
        // Detect if local database is mostly empty to request a full pull
        $isLocalEmpty = false;
        try {
            $count = $this->pdo->query("SELECT COUNT(*) FROM sales")->fetchColumn();
            if ($count == 0) {
                $isLocalEmpty = true;
            }
        } catch (\Exception $e) {}
        
        $exportUrl = $cloudUrl . '/api/sync/export';
        if ($isLocalEmpty) {
            $exportUrl .= '?full=1';
        }
        
        // 1. Request Export
        $ch = curl_init($exportUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'X-Sync-Api-Key: ' . $apiKey,
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MachineMVP-SyncClient/1.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if (!$response) {
            $this->jsonResponse(false, 'Failed to connect to cloud server for pull.');
        }
        
        $res = json_decode($response, true);
        
        if ($httpCode === 200 && $res && $res['success']) {
            $base64 = $res['message'];
            if (empty($base64) || $base64 === 'Everything is up to date.') {
                $this->jsonResponse(true, 'No new updates to pull.');
            }
            
            $decoded = base64_decode($base64);
            $jsonString = gzdecode($decoded);
            $data = json_decode($jsonString, true);
            
            if (!$data || !isset($data['tables'])) {
                $this->jsonResponse(false, 'Failed to parse pulled data.');
            }
            
            // 2. Process Pulled Data Locally
            $syncedIds = [];
            
            $isSqlite = \App\Config\Database::getDriver() === 'sqlite';
            if ($isSqlite) {
                $this->pdo->exec('PRAGMA foreign_keys = OFF;');
            } else {
                $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');
            }
            
            $this->pdo->beginTransaction();
            try {
                // Same strict foreign key order
                $tables = ['settings', 'users', 'customers', 'items', 'item_bundles', 'sales', 'sale_items', 'sale_returns', 'sale_return_items', 'payment_requests', 'inventory_logs', 'item_logs', 'debtors', 'debt_repayments', 'customer_debt_payments', 'expenditures', 'coffer_transactions', 'user_logins', 'payments'];
                
                foreach ($tables as $table) {
                    if (isset($data['tables'][$table]) && is_array($data['tables'][$table])) {
                        $this->upsertTable($table, $data['tables'][$table]);
                        
                        // Collect IDs to confirm back to cloud
                        if (!empty($data['tables'][$table])) {
                            $syncedIds[$table] = array_column($data['tables'][$table], 'id');
                        }
                    }
                }
                $this->pdo->commit();
            } catch (\Exception $e) {
                $this->pdo->rollBack();
                $this->jsonResponse(false, 'Error applying pulled data: ' . $e->getMessage());
            }

            if ($isSqlite) $this->pdo->exec('PRAGMA foreign_keys = ON;');
            else $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');
            
            // 3. Confirm Pull with Cloud
            if (!empty($syncedIds)) {
                $chConfirm = curl_init($cloudUrl . '/api/sync/confirm-pull');
                curl_setopt($chConfirm, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($chConfirm, CURLOPT_POST, true);
                curl_setopt($chConfirm, CURLOPT_POSTFIELDS, json_encode(['syncedIds' => $syncedIds]));
                curl_setopt($chConfirm, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey,
                    'X-Sync-Api-Key: ' . $apiKey,
                    'Accept: application/json'
                ]);
                curl_setopt($chConfirm, CURLOPT_USERAGENT, 'MachineMVP-SyncClient/1.0');
                curl_setopt($chConfirm, CURLOPT_TIMEOUT, 30);
                curl_setopt($chConfirm, CURLOPT_SSL_VERIFYPEER, false);
                curl_exec($chConfirm);
                curl_close($chConfirm);
            }
            
            $this->jsonResponse(true, 'Data pulled successfully.');
        } else {
            $errorMessage = $res['message'] ?? 'Unknown network error during pull.';
            $this->jsonResponse(false, 'Cloud Pull Failed: ' . $errorMessage . ' (HTTP ' . $httpCode . ')');
        }
    }
    
    private function jsonResponse($success, $message, $status = 200) {
        if (ob_get_length()) {
            ob_clean();
        }
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit;
    }
}
