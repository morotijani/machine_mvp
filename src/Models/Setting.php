<?php
namespace App\Models;

use PDO;

class Setting {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function get() {
        $stmt = $this->pdo->query("SELECT * FROM settings LIMIT 1");
        $settings = $stmt->fetch();
        if (!$settings) {
            // Should exist due to migration, but fallback
            $this->pdo->exec("INSERT INTO settings (company_name) VALUES ('My Company')");
            return $this->get();
        }
        return $settings;
    }

    public function update($data) {
        $sql = "UPDATE settings SET 
                company_name = :company_name,
                company_address = :company_address,
                company_phone = :company_phone,
                company_email = :company_email";
        
        $params = [
            'company_name' => $data['company_name'],
            'company_address' => $data['company_address'],
            'company_phone' => $data['company_phone'],
            'company_email' => $data['company_email']
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
