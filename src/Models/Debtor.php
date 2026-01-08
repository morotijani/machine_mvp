<?php
namespace App\Models;

use PDO;

class Debtor {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO standalone_debtors (name, phone, total_amount, description) VALUES (:name, :phone, :amount, :description)");
        return $stmt->execute([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'amount' => $data['total_amount'],
            'description' => $data['description']
        ]);
    }

    public function getAll($search = null) {
        $sql = "SELECT * FROM standalone_debtors WHERE is_deleted = 0";
        $params = [];
        if ($search) {
            $sql .= " AND (name LIKE :s1 OR phone LIKE :s2)";
            $params['s1'] = "%$search%";
            $params['s2'] = "%$search%";
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM standalone_debtors WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function recordPayment($debtorId, $amount, $date, $recordedBy) {
        $this->pdo->beginTransaction();
        try {
            // 1. Insert repayment record
            $stmt = $this->pdo->prepare("INSERT INTO debt_repayments (debtor_id, amount, payment_date, recorded_by) VALUES (:did, :amt, :date, :uid)");
            $stmt->execute([
                'did' => $debtorId,
                'amt' => $amount,
                'date' => $date,
                'uid' => $recordedBy
            ]);

            // 2. Update debtor totals
            $stmt = $this->pdo->prepare("UPDATE standalone_debtors SET paid_amount = paid_amount + :amt WHERE id = :did");
            $stmt->execute(['amt' => $amount, 'did' => $debtorId]);

            // 3. Update status
            $debtor = $this->find($debtorId);
            $newStatus = 'partially_paid';
            if ($debtor['paid_amount'] >= $debtor['total_amount']) {
                $newStatus = 'cleared';
            } elseif ($debtor['paid_amount'] <= 0) {
                $newStatus = 'unpaid';
            }

            $stmt = $this->pdo->prepare("UPDATE standalone_debtors SET status = :status WHERE id = :did");
            $stmt->execute(['status' => $newStatus, 'did' => $debtorId]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getHistory($debtorId) {
        $stmt = $this->pdo->prepare("SELECT r.*, u.username FROM debt_repayments r JOIN users u ON r.recorded_by = u.id WHERE r.debtor_id = :did ORDER BY r.payment_date DESC, r.created_at DESC");
        $stmt->execute(['did' => $debtorId]);
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE standalone_debtors SET name = :name, phone = :phone, total_amount = :amt, description = :desc WHERE id = :id");
        return $stmt->execute([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'amt' => $data['total_amount'],
            'desc' => $data['description'],
            'id' => $id
        ]);
    }

    public function softDelete($id) {
        $stmt = $this->pdo->prepare("UPDATE standalone_debtors SET is_deleted = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getTotalOutstanding() {
        $stmt = $this->pdo->query("SELECT SUM(total_amount - paid_amount) FROM standalone_debtors WHERE is_deleted = 0 AND status != 'cleared'");
        return $stmt->fetchColumn() ?: 0;
    }
}
