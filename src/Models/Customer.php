<?php
namespace App\Models;

use PDO;

class Customer {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        // Just basic info
        $stmt = $this->pdo->query("SELECT * FROM customers ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getWithDebt() {
        // Calculate debt
        $sql = "SELECT c.*, 
                (IFNULL(SUM(s.total_amount), 0) - IFNULL(SUM(s.paid_amount), 0)) as total_debt,
                MAX(s.created_at) as last_purchase
                FROM customers c
                LEFT JOIN sales s ON c.id = s.customer_id
                GROUP BY c.id
                ORDER BY total_debt DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($name, $phone, $address) {
        $stmt = $this->pdo->prepare("INSERT INTO customers (name, phone, address) VALUES (:name, :phone, :address)");
        $stmt->execute(['name' => $name, 'phone' => $phone, 'address' => $address]);
        return $this->pdo->lastInsertId();
    }
}
