<?php
namespace App\Models;

use PDO;

class Customer {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($limit = null, $offset = 0, $search = null) {
        $sql = "SELECT c.*, 
                (IFNULL(SUM(s.total_amount), 0) - IFNULL(SUM(s.paid_amount), 0)) as total_debt,
                MAX(s.created_at) as last_purchase
                FROM customers c
                LEFT JOIN sales s ON c.id = s.customer_id AND s.voided = 0";
        
        $params = [];
        
        if ($search) {
            $sql .= " WHERE c.name LIKE :search1 OR c.phone LIKE :search2";
            $params['search'] = "%$search%";
        }
        
        $sql .= " GROUP BY c.id ORDER BY total_debt DESC, c.name ASC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->pdo->prepare($sql);
        
        if ($search) {
            $stmt->bindValue(':search1', $params['search']);
            $stmt->bindValue(':search2', $params['search']);
        }
        
        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll($search = null) {
        $sql = "SELECT COUNT(*) FROM customers";
        $params = [];
        
        if ($search) {
            $sql .= " WHERE name LIKE :s1 OR phone LIKE :s2";
            $params['search'] = "%$search%";
        }
        
        $stmt = $this->pdo->prepare($sql);
        if ($search) {
            $stmt->bindValue(':s1', $params['search']);
            $stmt->bindValue(':s2', $params['search']);
            $stmt->execute();
        } else {
            $stmt->execute();
        }
        return $stmt->fetchColumn();
    }

    public function getWithDebt() {
        // Calculate debt
        $sql = "SELECT c.*, 
                (IFNULL(SUM(s.total_amount), 0) - IFNULL(SUM(s.paid_amount), 0)) as total_debt,
                MAX(s.created_at) as last_purchase
                FROM customers c
                LEFT JOIN sales s ON c.id = s.customer_id AND s.voided = 0
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
    public function update($id, $name, $phone, $address) {
        $stmt = $this->pdo->prepare("UPDATE customers SET name = :name, phone = :phone, address = :address WHERE id = :id");
        return $stmt->execute(['name' => $name, 'phone' => $phone, 'address' => $address, 'id' => $id]);
    }
}
