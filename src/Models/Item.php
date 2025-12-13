<?php
namespace App\Models;

use PDO;

class Item {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM items ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM items WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO items (name, category, sku, unit, price, cost_price, quantity, location, image_path) 
                VALUES (:name, :category, :sku, :unit, :price, :cost_price, :quantity, :location, :image_path)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE items SET name=:name, category=:category, sku=:sku, unit=:unit, 
                price=:price, cost_price=:cost_price, quantity=:quantity, location=:location 
                WHERE id=:id";
        $data['id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM items WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    // For Stock Control
    public function adjustStock($id, $quantityChange) {
        $sql = "UPDATE items SET quantity = quantity + :change WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['change' => $quantityChange, 'id' => $id]);
    }
}
