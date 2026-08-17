<?php
namespace App\Controllers;

use App\Config\Database;
use App\Utils\UUID;
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
        session_write_close();
        
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
            'debt_repayments', 'customer_debt_payments', 'coffer_transactions', 'standalone_debtors',
            'proformas', 'proforma_items'
        ];
        
        foreach ($optionalTables as $optTable) {
            try {
                $this->pdo->query("SELECT 1 FROM `$optTable` LIMIT 1");
                $tables[] = $optTable;
            } catch (\Exception $e) {
                // Table doesn't exist
            }
        }
        
        $payload = ['tables' => []];
        $syncedIds = [];
        
        // Populate UUIDs before syncing
        $this->populateUuidsBeforeSync($tables);
        
        foreach ($tables as $table) {
            // Fetch all unsynced records
            $stmt = $this->pdo->query("SELECT * FROM `$table` WHERE sync_status = 0 LIMIT 500");
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, $compressedPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/octet-stream',
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
    
    private function populateUuidsBeforeSync($tables) {
        $mappings = [
            'sales' => [
                ['new_col' => 'customer_uuid', 'old_col' => 'customer_id', 'ref_table' => 'customers'],
                ['new_col' => 'user_uuid', 'old_col' => 'user_id', 'ref_table' => 'users'],
            ],
            'sale_items' => [
                ['new_col' => 'sale_uuid', 'old_col' => 'sale_id', 'ref_table' => 'sales'],
                ['new_col' => 'item_uuid', 'old_col' => 'item_id', 'ref_table' => 'items'],
            ],
            'payments' => [
                ['new_col' => 'sale_uuid', 'old_col' => 'sale_id', 'ref_table' => 'sales'],
                ['new_col' => 'user_uuid', 'old_col' => 'recorded_by', 'ref_table' => 'users'],
            ],
            'sale_returns' => [
                ['new_col' => 'sale_uuid', 'old_col' => 'sale_id', 'ref_table' => 'sales'],
                ['new_col' => 'user_uuid', 'old_col' => 'recorded_by', 'ref_table' => 'users'],
            ],
            'sale_return_items' => [
                ['new_col' => 'return_uuid', 'old_col' => 'return_id', 'ref_table' => 'sale_returns'],
                ['new_col' => 'item_uuid', 'old_col' => 'item_id', 'ref_table' => 'items'],
            ],
            'payment_requests' => [
                ['new_col' => 'user_uuid', 'old_col' => 'created_by', 'ref_table' => 'users'],
                ['new_col' => 'customer_uuid', 'old_col' => 'customer_id', 'ref_table' => 'customers'],
            ],
            'inventory_logs' => [
                ['new_col' => 'item_uuid', 'old_col' => 'item_id', 'ref_table' => 'items'],
                ['new_col' => 'user_uuid', 'old_col' => 'user_id', 'ref_table' => 'users'],
            ],
            'item_bundles' => [
                ['new_col' => 'bundle_item_uuid', 'old_col' => 'bundle_item_id', 'ref_table' => 'items'],
                ['new_col' => 'component_item_uuid', 'old_col' => 'component_item_id', 'ref_table' => 'items'],
            ],
            'item_logs' => [
                ['new_col' => 'item_uuid', 'old_col' => 'item_id', 'ref_table' => 'items'],
                ['new_col' => 'user_uuid', 'old_col' => 'user_id', 'ref_table' => 'users'],
            ],
            'debtors' => [
                ['new_col' => 'customer_uuid', 'old_col' => 'customer_id', 'ref_table' => 'customers'],
            ],
            'standalone_debtors' => [
                ['new_col' => 'user_uuid', 'old_col' => 'recorded_by', 'ref_table' => 'users'],
            ],
            'customer_debt_payments' => [
                ['new_col' => 'customer_uuid', 'old_col' => 'customer_id', 'ref_table' => 'customers'],
                ['new_col' => 'user_uuid', 'old_col' => 'recorded_by', 'ref_table' => 'users'],
            ],
            'expenditures' => [
                ['new_col' => 'user_uuid', 'old_col' => 'recorded_by', 'ref_table' => 'users'],
            ],
            'coffer_transactions' => [
                ['new_col' => 'user_uuid', 'old_col' => 'recorded_by', 'ref_table' => 'users'],
            ],
            'user_logins' => [
                ['new_col' => 'user_uuid', 'old_col' => 'user_id', 'ref_table' => 'users'],
            ],
            'proformas' => [
                ['new_col' => 'customer_uuid', 'old_col' => 'customer_id', 'ref_table' => 'customers'],
                ['new_col' => 'user_uuid', 'old_col' => 'recorded_by', 'ref_table' => 'users'],
            ],
            'proforma_items' => [
                ['new_col' => 'proforma_uuid', 'old_col' => 'proforma_id', 'ref_table' => 'proformas'],
                ['new_col' => 'item_uuid', 'old_col' => 'item_id', 'ref_table' => 'items'],
            ],
        ];

        foreach ($tables as $table) {
            try {
                // Check if uuid column exists
                $stmt = $this->pdo->query("SELECT uuid FROM `$table` LIMIT 1");
            } catch (\Exception $e) {
                continue;
            }

            try {
                // Ensure primary uuid exists
                $stmt = $this->pdo->query("SELECT id FROM `$table` WHERE uuid IS NULL OR uuid = '' LIMIT 500");
                $missingUuidRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($missingUuidRows as $row) {
                    $uuid = \App\Utils\UUID::v4();
                    $updateStmt = $this->pdo->prepare("UPDATE `$table` SET uuid = ? WHERE id = ?");
                    $updateStmt->execute([$uuid, $row['id']]);
                }
                
                // Map foreign keys
                if (isset($mappings[$table])) {
                    foreach ($mappings[$table] as $map) {
                        $newCol = $map['new_col'];
                        $oldCol = $map['old_col'];
                        $refTable = $map['ref_table'];
                        
                        $sql = "UPDATE `$table` SET `$newCol` = (SELECT uuid FROM `$refTable` WHERE `$refTable`.id = `$table`.`$oldCol`) 
                                WHERE (`$newCol` IS NULL OR `$newCol` = '') AND `$oldCol` IS NOT NULL AND `$oldCol` > 0";
                        $this->pdo->exec($sql);
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }
    
    public function receiveDatabase() {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        $this->verifyApiKey();
        
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        // Handle both old raw JSON and new compressed form-data/octet-stream
        if (strpos($contentType, 'application/octet-stream') !== false) {
            $input = file_get_contents('php://input');
            $jsonString = gzdecode(base64_decode($input));
            $data = json_decode($jsonString, true);
        } elseif (isset($_POST['sync_data'])) {
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
            $tables = ['settings', 'users', 'customers', 'items', 'item_bundles', 'sales', 'sale_items', 'sale_returns', 'sale_return_items', 'payment_requests', 'inventory_logs', 'item_logs', 'debtors', 'standalone_debtors', 'debt_repayments', 'customer_debt_payments', 'expenditures', 'coffer_transactions', 'user_logins', 'payments'];
            
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
        
        // Reverse map foreign key UUIDs to local integer IDs
        $mappings = [
            'sales' => [
                'customer_id' => ['ref_table' => 'customers', 'uuid_col' => 'customer_uuid'],
                'user_id' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid']
            ],
            'sale_items' => [
                'sale_id' => ['ref_table' => 'sales', 'uuid_col' => 'sale_uuid'],
                'item_id' => ['ref_table' => 'items', 'uuid_col' => 'item_uuid']
            ],
            'payments' => [
                'sale_id' => ['ref_table' => 'sales', 'uuid_col' => 'sale_uuid'],
                'recorded_by' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid']
            ],
            'sale_returns' => [
                'sale_id' => ['ref_table' => 'sales', 'uuid_col' => 'sale_uuid'],
                'recorded_by' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid']
            ],
            'sale_return_items' => [
                'return_id' => ['ref_table' => 'sale_returns', 'uuid_col' => 'return_uuid'],
                'item_id' => ['ref_table' => 'items', 'uuid_col' => 'item_uuid']
            ],
            'inventory_logs' => [
                'item_id' => ['ref_table' => 'items', 'uuid_col' => 'item_uuid'],
                'user_id' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid']
            ],
            'item_bundles' => [
                'bundle_item_id' => ['ref_table' => 'items', 'uuid_col' => 'bundle_item_uuid'],
                'component_item_id' => ['ref_table' => 'items', 'uuid_col' => 'component_item_uuid']
            ],
            'item_logs' => [
                'item_id' => ['ref_table' => 'items', 'uuid_col' => 'item_uuid'],
                'user_id' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid']
            ],
            'debtors' => [
                'customer_id' => ['ref_table' => 'customers', 'uuid_col' => 'customer_uuid']
            ],
            'standalone_debtors' => [
                'recorded_by' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid']
            ],
            'customer_debt_payments' => [
                'customer_id' => ['ref_table' => 'customers', 'uuid_col' => 'customer_uuid'],
                'recorded_by' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid']
            ],
            'payment_requests' => [
                'created_by' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid'],
                'customer_id' => ['ref_table' => 'customers', 'uuid_col' => 'customer_uuid']
            ],
            'expenditures' => [
                'recorded_by' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid']
            ],
            'coffer_transactions' => [
                'recorded_by' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid']
            ],
            'user_logins' => [
                'user_id' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid']
            ],
            'proformas' => [
                'customer_id' => ['ref_table' => 'customers', 'uuid_col' => 'customer_uuid'],
                'recorded_by' => ['ref_table' => 'users', 'uuid_col' => 'user_uuid']
            ],
            'proforma_items' => [
                'proforma_id' => ['ref_table' => 'proformas', 'uuid_col' => 'proforma_uuid'],
                'item_id' => ['ref_table' => 'items', 'uuid_col' => 'item_uuid']
            ]
        ];

        if (isset($mappings[$table])) {
            foreach ($rows as &$row) {
                foreach ($mappings[$table] as $localIntCol => $map) {
                    $uuidCol = $map['uuid_col'];
                    $refTable = $map['ref_table'];
                    if (!empty($row[$uuidCol])) {
                        $stmt = $this->pdo->prepare("SELECT id FROM `$refTable` WHERE uuid = ?");
                        $stmt->execute([$row[$uuidCol]]);
                        $localId = $stmt->fetchColumn();
                        if ($localId) {
                            $row[$localIntCol] = $localId;
                        } else {
                            // Local ID not found, but it might just be 0/null allowed. Let it be what it was or null.
                        }
                    }
                }
            }
            unset($row); // break reference
        }

        // Remove 'id' column from insertion logic so local DB auto-increments
        $columns = [];
        foreach (array_keys($rows[0]) as $col) {
            if ($col !== 'id') {
                $columns[] = $col;
            }
        }

        // Only insert if the row has a uuid (essential for matching)
        if (!in_array('uuid', $columns)) {
            return;
        }

        $colNames = implode(', ', array_map(function($c) { return "`$c`"; }, $columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        
        $updateStmts = [];
        foreach ($columns as $col) {
            if ($col !== 'uuid') {
                if (\App\Config\Database::getDriver() === 'sqlite') {
                    $updateStmts[] = "`$col` = excluded.`$col`";
                } else {
                    $updateStmts[] = "`$col` = VALUES(`$col`)";
                }
            }
        }
        $updateSql = implode(', ', $updateStmts);
        
        if (\App\Config\Database::getDriver() === 'sqlite') {
            $sql = "INSERT INTO `$table` ($colNames) VALUES ($placeholders) ON CONFLICT(uuid) DO UPDATE SET $updateSql";
        } else {
            $sql = "INSERT INTO `$table` ($colNames) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateSql";
        }
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($rows as $row) {
            $values = [];
            foreach ($columns as $col) {
                if ($col === 'sync_status') {
                    $values[] = 1; // Force status to 1 on receipt to prevent ping-pong
                } else {
                    $values[] = $row[$col];
                }
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
        
        $tables = ['settings', 'users', 'customers', 'items', 'item_bundles', 'sales', 'sale_items', 'sale_returns', 'sale_return_items', 'payment_requests', 'inventory_logs', 'item_logs', 'debtors', 'standalone_debtors', 'debt_repayments', 'customer_debt_payments', 'expenditures', 'coffer_transactions', 'user_logins', 'payments', 'proformas', 'proforma_items'];
        
        // Before exporting, ensure all cloud records have UUIDs generated to prevent sync insertion failures
        $this->populateUuidsBeforeSync($tables);
        
        $payload = ['tables' => []];
        
        $isFull = isset($_GET['full']) && $_GET['full'] == '1';
        foreach ($tables as $table) {
            // Check if table exists (for backward compatibility if missing)
            try {
                $this->pdo->query("SELECT 1 FROM `$table` LIMIT 1");
                if ($isFull) {
                    $stmt = $this->pdo->query("SELECT * FROM `$table`");
                } else {
                    // Fetch all records modified on cloud (sync_status = 0) in chunks of 500 to avoid memory/payload limits
                    $stmt = $this->pdo->query("SELECT * FROM `$table` WHERE sync_status = 0 LIMIT 500");
                }
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($rows) > 0) {
                    $payload['tables'][$table] = $rows;
                }
            } catch (\Exception $e) {
                // Table doesn't exist
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
        \App\Middleware\AuthMiddleware::requireLogin();
        \App\Middleware\AuthMiddleware::requireAdmin();
        session_write_close();
        
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
                $tables = ['settings', 'users', 'customers', 'items', 'item_bundles', 'sales', 'sale_items', 'sale_returns', 'sale_return_items', 'payment_requests', 'inventory_logs', 'item_logs', 'debtors', 'standalone_debtors', 'debt_repayments', 'customer_debt_payments', 'expenditures', 'coffer_transactions', 'user_logins', 'payments'];
                
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
