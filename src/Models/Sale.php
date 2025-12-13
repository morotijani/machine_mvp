<?php
namespace App\Models;

use PDO;
use Exception;

class Sale {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createSale($customerId, $userId, $items, $paymentAmount) {
        try {
            $this->pdo->beginTransaction();

            // 1. Calculate Total
            $totalAmount = 0;
            foreach ($items as $item) {
                // Verify price and stock
                $stmt = $this->pdo->prepare("SELECT price, quantity FROM items WHERE id = :id FOR UPDATE");
                $stmt->execute(['id' => $item['id']]);
                $dbItem = $stmt->fetch();
                
                if (!$dbItem) throw new Exception("Item not found: " . $item['id']);
                if ($dbItem['quantity'] < $item['quantity']) throw new Exception("Insufficient stock for item ID: " . $item['id']);
                
                $totalAmount += $dbItem['price'] * $item['quantity'];
            }

            // 2. Determine Status
            $status = 'unpaid';
            if ($paymentAmount >= $totalAmount) {
                $status = 'paid';
                $paymentAmount = $totalAmount; // Cap at total ?? Or should we return change? Assuming exact or partial.
            } elseif ($paymentAmount > 0) {
                $status = 'partial';
            }

            // 3. Create Sale Record
            $stmt = $this->pdo->prepare("INSERT INTO sales (customer_id, user_id, total_amount, paid_amount, payment_status) VALUES (:cid, :uid, :total, :paid, :status)");
            $stmt->execute([
                'cid' => $customerId,
                'uid' => $userId,
                'total' => $totalAmount,
                'paid' => $paymentAmount,
                'status' => $status
            ]);
            $saleId = $this->pdo->lastInsertId();

            // 4. Add Sale Items and Update Stock
            foreach ($items as $item) {
                $stmt = $this->pdo->prepare("SELECT price FROM items WHERE id = :id");
                $stmt->execute(['id' => $item['id']]);
                $price = $stmt->fetchColumn();
                $subtotal = $price * $item['quantity'];

                // Insert Line Item
                $stmt = $this->pdo->prepare("INSERT INTO sale_items (sale_id, item_id, quantity, price_at_sale, subtotal) VALUES (:sid, :iid, :qty, :price, :subtotal)");
                $stmt->execute([
                    'sid' => $saleId,
                    'iid' => $item['id'],
                    'qty' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $subtotal
                ]);

                // Update Inventory
                $stmt = $this->pdo->prepare("UPDATE items SET quantity = quantity - :qty WHERE id = :id");
                $stmt->execute(['qty' => $item['quantity'], 'id' => $item['id']]);
            }

            // 5. Record Payment
            if ($paymentAmount > 0) {
                $stmt = $this->pdo->prepare("INSERT INTO payments (sale_id, amount, recorded_by) VALUES (:sid, :amount, :uid)");
                $stmt->execute(['sid' => $saleId, 'amount' => $paymentAmount, 'uid' => $userId]);
            }

            $this->pdo->commit();
            return $saleId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getAll($filters = [], $page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        $params = [];
        $where = ["1=1"];

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $where[] = "(s.id = :search OR c.name LIKE :searchLike)";
            $params['search'] = $term; // For exact ID match if numeric
            $params['searchLike'] = "%$term%";
        }

        if (!empty($filters['start_date'])) {
            $where[] = "DATE(s.created_at) >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $where[] = "DATE(s.created_at) <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $where[] = "s.payment_status = :status";
            $params['status'] = $filters['status'];
        }

        $whereSql = implode(" AND ", $where);

        $sql = "SELECT s.*, c.name as customer_name, u.username as seller_name 
                FROM sales s 
                LEFT JOIN customers c ON s.customer_id = c.id 
                JOIN users u ON s.user_id = u.id 
                WHERE $whereSql
                ORDER BY s.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll($filters = []) {
        $params = [];
        $where = ["1=1"];

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $where[] = "(s.id = :search OR c.name LIKE :searchLike)";
            $params['search'] = $term;
            $params['searchLike'] = "%$term%";
        }

        if (!empty($filters['start_date'])) {
            $where[] = "DATE(s.created_at) >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $where[] = "DATE(s.created_at) <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $where[] = "s.payment_status = :status";
            $params['status'] = $filters['status'];
        }

        $whereSql = implode(" AND ", $where);

        $sql = "SELECT COUNT(*) 
                FROM sales s 
                LEFT JOIN customers c ON s.customer_id = c.id 
                WHERE $whereSql";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function getById($id) {
        $sql = "SELECT s.*, c.name as customer_name, c.address as customer_address, c.phone as customer_phone, u.username as seller_name 
                FROM sales s 
                LEFT JOIN customers c ON s.customer_id = c.id 
                JOIN users u ON s.user_id = u.id 
                WHERE s.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $sale = $stmt->fetch();
        
        if ($sale) {
            $stmtItems = $this->pdo->prepare("SELECT si.*, i.name as item_name, i.sku FROM sale_items si JOIN items i ON si.item_id = i.id WHERE si.sale_id = :id");
            $stmtItems->execute(['id' => $id]);
            $sale['items'] = $stmtItems->fetchAll();
        }
        return $sale;
    }
}
