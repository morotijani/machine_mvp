<?php
namespace App\Models;

use PDO;

class Expenditure {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO expenditures (category, amount, description, date, recorded_by) VALUES (:category, :amount, :description, :date, :recorded_by)");
        return $stmt->execute([
            'category' => $data['category'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'date' => $data['date'],
            'recorded_by' => $data['recorded_by']
        ]);
    }

    public function getAll($limit = 20, $offset = 0, $search = null, $userId = null) {
        $sql = "SELECT e.*, u.username as recorder_name 
                FROM expenditures e 
                LEFT JOIN users u ON e.recorded_by = u.id
                WHERE e.is_deleted = 0";
        
        $params = [];
        if ($userId) {
            $sql .= " AND e.recorded_by = :userId";
            $params['userId'] = $userId;
        }

        if ($search) {
            $sql .= " AND (e.category LIKE :s1 OR e.description LIKE :s2)";
            $params['s1'] = "%$search%";
            $params['s2'] = "%$search%";
        }

        $sql .= " ORDER BY e.date DESC, e.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll($search = null, $userId = null) {
        $sql = "SELECT COUNT(*) FROM expenditures WHERE is_deleted = 0";
        $params = [];
        if ($userId) {
            $sql .= " AND recorded_by = :userId";
            $params['userId'] = $userId;
        }
        if ($search) {
            $sql .= " AND (category LIKE :s1 OR description LIKE :s2)";
            $params['s1'] = "%$search%";
            $params['s2'] = "%$search%";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM expenditures WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE expenditures SET category = :category, amount = :amount, description = :description, date = :date WHERE id = :id");
        return $stmt->execute([
            'category' => $data['category'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'date' => $data['date'],
            'id' => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("UPDATE expenditures SET is_deleted = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function restore($id) {
        $stmt = $this->pdo->prepare("UPDATE expenditures SET is_deleted = 0 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function hardDelete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM expenditures WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getDeleted() {
        $stmt = $this->pdo->query("SELECT e.*, u.username as recorder_name FROM expenditures e LEFT JOIN users u ON e.recorded_by = u.id WHERE e.is_deleted = 1 ORDER BY e.updated_at DESC");
        return $stmt->fetchAll();
    }

    public function getDailyTotal($date, $userId = null) {
        $sql = "SELECT SUM(amount) FROM expenditures WHERE date = :date AND is_deleted = 0";
        $params = ['date' => $date];
        if ($userId) {
            $sql .= " AND recorded_by = :userId";
            $params['userId'] = $userId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() ?: 0;
    }

    public function getMonthlyTotal($month, $year, $userId = null) {
        $sql = "SELECT SUM(amount) FROM expenditures WHERE MONTH(date) = :m AND YEAR(date) = :y AND is_deleted = 0";
        $params = ['m' => $month, 'y' => $year];
        if ($userId) {
            $sql .= " AND recorded_by = :userId";
            $params['userId'] = $userId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() ?: 0;
    }
}
