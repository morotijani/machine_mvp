<?php
namespace App\Models;

use PDO;

class Customer {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($limit = null, $offset = 0, $search = null, $sort = 'total_debt', $order = 'DESC') {
        $sql = "SELECT c.*, 
                (IFNULL(SUM(s.total_amount), 0) - IFNULL(SUM(s.paid_amount), 0)) as total_debt,
                MAX(s.created_at) as last_purchase
                FROM customers c
                LEFT JOIN sales s ON c.id = s.customer_id AND s.voided = 0
                WHERE c.is_deleted = 0";
        
        $params = [];
        
        if ($search) {
            $sql .= " AND (c.name LIKE :search1 OR c.phone LIKE :search2)";
            $params['search'] = "%$search%";
        }
        
        // Whitelist for sorting
        $allowedSort = ['name', 'total_debt', 'last_purchase'];
        $allowedOrder = ['ASC', 'DESC'];
        
        if (!in_array($sort, $allowedSort)) $sort = 'total_debt';
        if (!in_array(strtoupper($order), $allowedOrder)) $order = 'DESC';
        
        $sql .= " GROUP BY c.id ORDER BY $sort $order";
        
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
        $sql = "SELECT COUNT(*) FROM customers WHERE is_deleted = 0";
        $params = [];
        
        if ($search) {
            $sql .= " AND (name LIKE :s1 OR phone LIKE :s2)";
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
                WHERE c.is_deleted = 0
                GROUP BY c.id
                ORDER BY total_debt DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    public function find($id) {
        $sql = "SELECT c.*, 
                (IFNULL(SUM(s.total_amount), 0) - IFNULL(SUM(s.paid_amount), 0)) as total_debt,
                IFNULL(SUM(s.paid_amount), 0) as total_paid,
                IFNULL(SUM(s.total_amount), 0) as total_sales_amount
                FROM customers c
                LEFT JOIN sales s ON c.id = s.customer_id AND s.voided = 0
                WHERE c.id = :id
                GROUP BY c.id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getHistory($customerId) {
        // Fetch all sales for this customer with payment status and item summary
        $sql = "SELECT s.*, 
                (s.total_amount - s.paid_amount) as balance,
                u.username as seller_name,
                GROUP_CONCAT(CONCAT(i.name, ' (', si.quantity, ')') SEPARATOR ', ') as items_summary
                FROM sales s
                LEFT JOIN users u ON s.user_id = u.id
                LEFT JOIN sale_items si ON s.id = si.sale_id
                LEFT JOIN items i ON si.item_id = i.id
                WHERE s.customer_id = :cid
                GROUP BY s.id
                ORDER BY s.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cid' => $customerId]);
        return $stmt->fetchAll();
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

    public function findByPhone($phone, $excludeId = null) {
        $sql = "SELECT * FROM customers WHERE phone = :phone";
        $params = ['phone' => $phone];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function softDelete($id) {
        $stmt = $this->pdo->prepare("UPDATE customers SET is_deleted = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function restore($id) {
        $stmt = $this->pdo->prepare("UPDATE customers SET is_deleted = 0 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getDeleted() {
        $sql = "SELECT c.*, 
                (IFNULL(SUM(s.total_amount), 0) - IFNULL(SUM(s.paid_amount), 0)) as total_debt,
                MAX(s.created_at) as last_purchase
                FROM customers c
                LEFT JOIN sales s ON c.id = s.customer_id AND s.voided = 0
                WHERE c.is_deleted = 1
                GROUP BY c.id
                ORDER BY c.name ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function countDeleted() {
        return $this->pdo->query("SELECT COUNT(*) FROM customers WHERE is_deleted = 1")->fetchColumn();
    }

    public function hardDelete($id) {
        // Block if transactions exist
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM sales WHERE customer_id = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new \Exception("Cannot delete customer with transaction history.");
        }
        $stmt = $this->pdo->prepare("DELETE FROM customers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
