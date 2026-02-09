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

        if (!empty($filters['delete_request'])) {
            $where[] = "s.delete_request_status = :del_status";
            $params['del_status'] = $filters['delete_request'];
        }

        if (!empty($filters['user_id'])) {
            $where[] = "s.user_id = :uid";
            $params['uid'] = $filters['user_id'];
        }

        // Voided filtering
        if (isset($filters['show_voided']) && $filters['show_voided'] !== 'all') {
            $where[] = "s.voided = :voided";
            $params['voided'] = ($filters['show_voided'] === 'yes') ? 1 : 0;
        } elseif (!isset($filters['show_voided'])) {
            // Default: hide voided
            $where[] = "s.voided = 0";
        }

        $whereSql = implode(" AND ", $where);

        $sql = "SELECT s.*, c.name as customer_name, c.is_deleted as customer_is_deleted, u.username as seller_name 
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

        if (!empty($filters['delete_request'])) {
            $where[] = "s.delete_request_status = :del_status";
            $params['del_status'] = $filters['delete_request'];
        }

        if (!empty($filters['user_id'])) {
            $where[] = "s.user_id = :uid";
            $params['uid'] = $filters['user_id'];
        }

        // Voided filtering
        if (isset($filters['show_voided']) && $filters['show_voided'] !== 'all') {
            $where[] = "s.voided = :voided";
            $params['voided'] = ($filters['show_voided'] === 'yes') ? 1 : 0;
        } elseif (!isset($filters['show_voided'])) {
            // Default: hide voided
            $where[] = "s.voided = 0";
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
        $sql = "SELECT s.*, c.name as customer_name, c.is_deleted as customer_is_deleted, c.address as customer_address, c.phone as customer_phone, u.username as seller_name 
                FROM sales s 
                LEFT JOIN customers c ON s.customer_id = c.id 
                JOIN users u ON s.user_id = u.id 
                WHERE s.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $sale = $stmt->fetch();
        
        if ($sale) {
            $stmtItems = $this->pdo->prepare("SELECT si.*, COALESCE(i.name, '[Deleted Item]') as item_name, i.sku FROM sale_items si LEFT JOIN items i ON si.item_id = i.id WHERE si.sale_id = :id");
            $stmtItems->execute(['id' => $id]);
            $sale['items'] = $stmtItems->fetchAll();
        }
        return $sale;
    }
    public function requestDelete($id) {
        $stmt = $this->pdo->prepare("UPDATE sales SET delete_request_status = 'pending', delete_requested_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function approveDelete($id) {
        try {
            $this->pdo->beginTransaction();

            // 0. Check if already approved to prevent double restoration
            $stmt = $this->pdo->prepare("SELECT delete_request_status, voided FROM sales WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $id]);
            $current = $stmt->fetch();

            if ($current['delete_request_status'] === 'approved' || $current['voided'] == 1) {
                $this->pdo->rollBack();
                return true; // Already processed
            }

            // 1. Get Sale Items
            $stmt = $this->pdo->prepare("SELECT item_id, quantity FROM sale_items WHERE sale_id = :id");
            $stmt->execute(['id' => $id]);
            $items = $stmt->fetchAll();

            // 2. Restore Stock
            foreach ($items as $item) {
                $stmtStock = $this->pdo->prepare("UPDATE items SET quantity = quantity + :qty WHERE id = :id");
                $stmtStock->execute(['qty' => $item['quantity'], 'id' => $item['item_id']]);
            }

            // 3. Mark as Approved and Voided
            $stmt = $this->pdo->prepare("UPDATE sales SET delete_request_status = 'approved', voided = 1 WHERE id = :id");
            $stmt->execute(['id' => $id]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function rejectDelete($id) {
        $stmt = $this->pdo->prepare("UPDATE sales SET delete_request_status = 'rejected' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getDeleted() {
        $stmt = $this->pdo->query("SELECT s.*, c.name as customer_name FROM sales s JOIN customers c ON s.customer_id = c.id WHERE s.voided = 1 ORDER BY s.created_at DESC");
        return $stmt->fetchAll();
    }

    public function restore($id) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("SELECT si.* FROM sale_items si WHERE si.sale_id = :id");
            $stmt->execute(['id' => $id]);
            $items = $stmt->fetchAll();

            foreach ($items as $item) {
                $stmtStock = $this->pdo->prepare("UPDATE items SET quantity = quantity - :qty WHERE id = :id");
                $stmtStock->execute(['qty' => $item['quantity'], 'id' => $item['item_id']]);
            }

            $stmt = $this->pdo->prepare("UPDATE sales SET voided = 0, delete_request_status = 'none' WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function hardDelete($id) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("DELETE FROM payments WHERE sale_id = :id");
            $stmt->execute(['id' => $id]);
            $stmt = $this->pdo->prepare("DELETE FROM sale_items WHERE sale_id = :id");
            $stmt->execute(['id' => $id]);
            $stmt = $this->pdo->prepare("DELETE FROM sales WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function processReturn($saleId, $returnedItems, $userId) {
        try {
            $this->pdo->beginTransaction();

            $sale = $this->getById($saleId);
            if (!$sale || $sale['voided']) {
                throw new Exception("Sale not found or voided.");
            }

            $totalDeduction = 0;
            foreach ($returnedItems as $itemId => $qty) {
                if ($qty <= 0) continue;

                // 1. Find the original sale item
                $stmt = $this->pdo->prepare("SELECT * FROM sale_items WHERE sale_id = :sid AND item_id = :iid");
                $stmt->execute(['sid' => $saleId, 'iid' => $itemId]);
                $originalItem = $stmt->fetch();

                if (!$originalItem) {
                    throw new Exception("Item ID $itemId not found in this sale.");
                }

                if ($qty > $originalItem['quantity']) {
                    throw new Exception("Cannot return more than purchased for item ID $itemId.");
                }

                $itemDeduction = $qty * $originalItem['price_at_sale'];
                $totalDeduction += $itemDeduction;

                // 2. Update sale_items record
                $stmt = $this->pdo->prepare("UPDATE sale_items SET quantity = quantity - :qty, subtotal = subtotal - :deduction WHERE id = :id");
                $stmt->execute(['qty' => $qty, 'deduction' => $itemDeduction, 'id' => $originalItem['id']]);

                // 3. Restore stock in items table
                $stmt = $this->pdo->prepare("UPDATE items SET quantity = quantity + :qty WHERE id = :id");
                $stmt->execute(['qty' => $qty, 'id' => $itemId]);
            }

            if ($totalDeduction <= 0) {
                $this->pdo->rollBack();
                return true;
            }

            // 4. Create sale_returns record
            $stmt = $this->pdo->prepare("INSERT INTO sale_returns (sale_id, total_deduction, recorded_by) VALUES (:sid, :deduction, :uid)");
            $stmt->execute(['sid' => $saleId, 'deduction' => $totalDeduction, 'uid' => $userId]);
            $returnId = $this->pdo->lastInsertId();

            // 5. Create sale_return_items records
            foreach ($returnedItems as $itemId => $qty) {
                if ($qty <= 0) continue;
                
                $stmt = $this->pdo->prepare("SELECT price_at_sale FROM sale_items WHERE sale_id = :sid AND item_id = :iid");
                $stmt->execute(['sid' => $saleId, 'iid' => $itemId]);
                $priceAtSale = $stmt->fetchColumn();

                $stmt = $this->pdo->prepare("INSERT INTO sale_return_items (return_id, item_id, quantity, price_at_sale) VALUES (:rid, :iid, :qty, :price)");
                $stmt->execute(['rid' => $returnId, 'iid' => $itemId, 'qty' => $qty, 'price' => $priceAtSale]);
            }

            // 6. Update main sales record (total_amount)
            $newTotal = $sale['total_amount'] - $totalDeduction;
            $paidAmount = $sale['paid_amount'];

            // If customer has already paid more than the new total, we cap paid_amount (effectively record a refund)
            if ($paidAmount > $newTotal) {
                $paidAmount = $newTotal;
            }

            // Determine new status
            $status = 'unpaid';
            if ($paidAmount >= $newTotal) {
                $status = 'paid';
            } elseif ($paidAmount > 0) {
                $status = 'partial';
            }

            $stmt = $this->pdo->prepare("UPDATE sales SET total_amount = :total, paid_amount = :paid, payment_status = :status WHERE id = :id");
            $stmt->execute(['total' => $newTotal, 'paid' => $paidAmount, 'status' => $status, 'id' => $saleId]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
