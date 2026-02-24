<?php
namespace App\Models;

use PDO;

class Payment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function recordPayment($saleId, $amount, $userId) {
        try {
            $this->pdo->beginTransaction();

            // 1. Get Sale Info
            $stmt = $this->pdo->prepare("SELECT customer_id, total_amount, paid_amount FROM sales WHERE id = :id");
            $stmt->execute(['id' => $saleId]);
            $sale = $stmt->fetch();
            
            if (!$sale) {
                throw new \Exception("Sale not found");
            }

            // 2. Create Debt Payment Event
            $stmtDebt = $this->pdo->prepare("
                INSERT INTO customer_debt_payments (customer_id, amount, recorded_by, notes) 
                VALUES (:cid, :amount, :uid, :notes)
            ");
            $stmtDebt->execute([
                'cid' => $sale['customer_id'],
                'amount' => $amount,
                'uid' => $userId,
                'notes' => "Payment for Invoice #$saleId"
            ]);
            $debtPaymentId = $this->pdo->lastInsertId();

            $newPaidAmount = $sale['paid_amount'] + $amount;
            if ($newPaidAmount > $sale['total_amount']) {
                 $newPaidAmount = $sale['total_amount'];
            }

            $status = 'partial';
            if ($newPaidAmount >= $sale['total_amount']) {
                $status = 'paid';
            }

            // 3. Insert Payment Record
            $stmt = $this->pdo->prepare("INSERT INTO payments (sale_id, amount, recorded_by, customer_debt_payment_id) VALUES (:sid, :amount, :uid, :dpid)");
            $stmt->execute(['sid' => $saleId, 'amount' => $amount, 'uid' => $userId, 'dpid' => $debtPaymentId]);

            // 4. Update Sale
            $stmt = $this->pdo->prepare("UPDATE sales SET paid_amount = :paid, payment_status = :status WHERE id = :id");
            $stmt->execute(['paid' => $newPaidAmount, 'status' => $status, 'id' => $saleId]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            return false;
        }
    }

    public function getHistoryBySale($saleId) {
        $stmt = $this->pdo->prepare("SELECT p.*, u.username FROM payments p JOIN users u ON p.recorded_by = u.id WHERE p.sale_id = :sid ORDER BY p.payment_date DESC");
        $stmt->execute(['sid' => $saleId]);
        return $stmt->fetchAll();
    }
}
