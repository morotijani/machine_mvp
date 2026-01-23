<?php
namespace App\Controllers;

use App\Config\Database;
use Exception;

class StatusController {
    public function check() {
        header('Content-Type: application/json');
        
        $status = [
            'database' => false,
            'time' => date('Y-m-d H:i:s'),
            'authenticated' => isset($_SESSION['user_id'])
        ];

        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->query("SELECT 1");
            if ($stmt) {
                $status['database'] = true;
            }
        } catch (Exception $e) {
            $status['error'] = $e->getMessage();
        }

        echo json_encode($status);
        exit;
    }
}
