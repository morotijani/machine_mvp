<?php
namespace App\Models;

use PDO;

class Proforma {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($data, $items) {
        $this->pdo->beginTransaction();

        try {
            // Generate unique reference number (e.g., PF-YYYYMMDD-XXXX)
            $datePrefix = date('Ymd');
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM proformas WHERE reference_no LIKE 'PF-$datePrefix-%'");
            $count = $stmt->fetchColumn() + 1;
            $referenceNo = sprintf("PF-%s-%04d", $datePrefix, $count);

            $stmt = $this->pdo->prepare("
                INSERT INTO proformas (reference_no, customer_id, total_amount, recorded_by, notes) 
                VALUES (:reference_no, :customer_id, :total_amount, :recorded_by, :notes)
            ");
            
            $stmt->execute([
                'reference_no' => $referenceNo,
                'customer_id' => $data['customer_id'] ?: null,
                'total_amount' => $data['total_amount'],
                'recorded_by' => $data['recorded_by'],
                'notes' => $data['notes'] ?? null
            ]);
            
            $proformaId = $this->pdo->lastInsertId();

            $stmtItems = $this->pdo->prepare("
                INSERT INTO proforma_items (proforma_id, item_id, quantity, price_at_time) 
                VALUES (:proforma_id, :item_id, :quantity, :price_at_time)
            ");

            foreach ($items as $item) {
                $stmtItems->execute([
                    'proforma_id' => $proformaId,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'price_at_time' => $item['price']
                ]);
            }

            $this->pdo->commit();
            return $proformaId;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.name as customer_name, c.phone as customer_phone, c.address as customer_address, u.username as recorder_name 
            FROM proformas p 
            LEFT JOIN customers c ON p.customer_id = c.id 
            LEFT JOIN users u ON p.recorded_by = u.id 
            WHERE p.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getItems($proformaId) {
        $stmt = $this->pdo->prepare("
            SELECT pi.*, i.name, i.sku, i.type as item_type 
            FROM proforma_items pi 
            JOIN items i ON pi.item_id = i.id 
            WHERE pi.proforma_id = :proforma_id
        ");
        $stmt->execute(['proforma_id' => $proformaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBundleComponents($itemId) {
        // Just reusing the bundle items logic
        $stmt = $this->pdo->prepare("
            SELECT bi.child_item_id, bi.quantity, i.name, i.sku as child_sku, i.price as selling_price 
            FROM item_bundles bi
            JOIN items i ON bi.child_item_id = i.id
            WHERE bi.parent_item_id = :item_id
        ");
        $stmt->execute(['item_id' => $itemId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT p.*, c.name as customer_name, u.username as recorder_name 
            FROM proformas p 
            LEFT JOIN customers c ON p.customer_id = c.id 
            LEFT JOIN users u ON p.recorded_by = u.id 
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        try {
            $this->pdo->beginTransaction();
            
            // Delete items first
            $stmt = $this->pdo->prepare("DELETE FROM proforma_items WHERE proforma_id = :id");
            $stmt->execute(['id' => $id]);
            
            // Delete proforma
            $stmt = $this->pdo->prepare("DELETE FROM proformas WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}
