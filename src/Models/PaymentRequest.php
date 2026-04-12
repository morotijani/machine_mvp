<?php
namespace App\Models;

use PDO;

class PaymentRequest {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($type, $reference_id, $amount_due, $created_by, $customer_id = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO payment_requests (type, reference_id, amount_due, created_by, customer_id) 
            VALUES (:type, :ref_id, :amount, :created_by, :customer_id)
        ");
        $stmt->execute([
            'type' => $type,
            'ref_id' => $reference_id,
            'amount' => $amount_due,
            'created_by' => $created_by,
            'customer_id' => $customer_id
        ]);
        return $this->pdo->lastInsertId();
    }

    public function getPending() {
        $sql = "SELECT pr.*, u.username as creator_name, c.name as customer_name
                FROM payment_requests pr
                JOIN users u ON pr.created_by = u.id
                LEFT JOIN customers c ON pr.customer_id = c.id OR (pr.type = 'debt_single' AND pr.reference_id = c.id)
                WHERE pr.status = 'pending'
                ORDER BY pr.created_at ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT pr.*, c.name as customer_name FROM payment_requests pr LEFT JOIN customers c ON pr.customer_id = c.id WHERE pr.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function endorse($id, $cashierId) {
        $stmt = $this->pdo->prepare("
            UPDATE payment_requests 
            SET status = 'approved', cashier_id = :cashier_id, processed_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'cashier_id' => $cashierId
        ]);
    }

    public function reject($id, $cashierId) {
        $stmt = $this->pdo->prepare("
            UPDATE payment_requests 
            SET status = 'rejected', cashier_id = :cashier_id, processed_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'cashier_id' => $cashierId
        ]);
    }
}
