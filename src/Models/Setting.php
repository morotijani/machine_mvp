<?php
namespace App\Models;

use PDO;

class Setting
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function get()
    {
        $stmt = $this->pdo->query("SELECT * FROM settings LIMIT 1");
        $settings = $stmt->fetch();
        if (!$settings) {
            // Should exist due to migration, but fallback
            $this->pdo->exec("INSERT INTO settings (company_name) VALUES ('POS LITE')");
            $stmt = $this->pdo->query("SELECT * FROM settings LIMIT 1");
            return $stmt->fetch();
        }
        return $settings;
    }

    public function update($data)
    {
        $sql = "UPDATE settings SET 
                company_name = :company_name,
                company_address = :company_address,
                company_phone = :company_phone,
                company_email = :company_email,
                receipt_type = :receipt_type,
                enable_debt_module = :enable_debt_module,
                enable_barcode_reader = :enable_barcode_reader,
                enable_desktop_setup = :enable_desktop_setup,
                cloud_url = :cloud_url,
                sync_api_key = :sync_api_key,
                sync_master_enabled = :sync_master_enabled,
                sync_auto_enabled = :sync_auto_enabled,
                sync_push_enabled = :sync_push_enabled,
                sync_pull_enabled = :sync_pull_enabled,
                sync_interval_minutes = :sync_interval_minutes,
                sync_status = 0";

        $params = [
            'company_name' => $data['company_name'],
            'company_address' => $data['company_address'],
            'company_phone' => $data['company_phone'],
            'company_email' => $data['company_email'],
            'receipt_type' => $data['receipt_type'] ?? 'a4',
            'enable_debt_module' => $data['enable_debt_module'] ?? 1,
            'enable_barcode_reader' => $data['enable_barcode_reader'] ?? 1,
            'enable_desktop_setup' => isset($data['enable_desktop_setup']) ? 1 : 0,
            'cloud_url' => $data['cloud_url'] ?? null,
            'sync_api_key' => $data['sync_api_key'] ?? null,
            'sync_master_enabled' => isset($data['sync_master_enabled']) ? 1 : 0,
            'sync_auto_enabled' => isset($data['sync_auto_enabled']) ? 1 : 0,
            'sync_push_enabled' => isset($data['sync_push_enabled']) ? 1 : 0,
            'sync_pull_enabled' => isset($data['sync_pull_enabled']) ? 1 : 0,
            'sync_interval_minutes' => isset($data['sync_interval_minutes']) ? (int)$data['sync_interval_minutes'] : 5,
        ];

        if (isset($data['company_logo'])) {
            $sql .= ", company_logo = :company_logo";
            $params['company_logo'] = $data['company_logo'];
        }

        // We assume single row, but good practice to limit
        // Or could add WHERE id = 1 if strictly enforced
        $sql .= " WHERE id = :id";
        $params['id'] = $data['id'];

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
