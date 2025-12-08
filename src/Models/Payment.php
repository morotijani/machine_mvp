<?php
namespace App\Models;

use PDO;

class Payment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function recordPayment($saleId, $amount, $userId) {
        // 1. Get Sale Info
        $stmt = $this->pdo->prepare("SELECT total_amount, paid_amount FROM sales WHERE id = :id");
        $stmt->execute(['id' => $saleId]);
        $sale = $stmt->fetch();
        
        if (!$sale) {
            throw new \Exception("Sale not found");
        }

        $newPaidAmount = $sale['paid_amount'] + $amount;
        if ($newPaidAmount > $sale['total_amount']) {
             $newPaidAmount = $sale['total_amount']; // Auto-cap?
        }

        $status = 'partial';
        if ($newPaidAmount >= $sale['total_amount']) {
            $status = 'paid';
        }

        try {
            $this->pdo->beginTransaction();

            // 2. Insert Payment Record
            $stmt = $this->pdo->prepare("INSERT INTO payments (sale_id, amount, recorded_by) VALUES (:sid, :amount, :uid)");
            $stmt->execute(['sid' => $saleId, 'amount' => $amount, 'uid' => $userId]);

            // 3. Update Sale
            $stmt = $this->pdo->prepare("UPDATE sales SET paid_amount = :paid, payment_status = :status WHERE id = :id");
            $stmt->execute(['paid' => $newPaidAmount, 'status' => $status, 'id' => $saleId]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function getHistoryBySale($saleId) {
        $stmt = $this->pdo->prepare("SELECT p.*, u.username FROM payments p JOIN users u ON p.recorded_by = u.id WHERE p.sale_id = :sid ORDER BY p.payment_date DESC");
        $stmt->execute(['sid' => $saleId]);
        return $stmt->fetchAll();
    }
}
