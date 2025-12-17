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
        if (array_key_exists('image_path', $data)) {
            $sql = "UPDATE items SET name=:name, category=:category, sku=:sku, unit=:unit, 
                    price=:price, cost_price=:cost_price, quantity=:quantity, location=:location, image_path=:image_path 
                    WHERE id=:id";
        } else {
             $sql = "UPDATE items SET name=:name, category=:category, sku=:sku, unit=:unit, 
                    price=:price, cost_price=:cost_price, quantity=:quantity, location=:location 
                    WHERE id=:id";
        }
        $data['id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM items WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function adjustStock($id, $quantityChange) {
        $sql = "UPDATE items SET quantity = quantity + :change WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['change' => $quantityChange, 'id' => $id]);
    }

    public function createBundle($data, $components) {
        try {
            $this->pdo->beginTransaction();

            // 1. Verify Stock for all components
            $totalCost = 0;
            foreach ($components as $comp) {
                $stmt = $this->pdo->prepare("SELECT quantity, cost_price FROM items WHERE id = :id FOR UPDATE");
                $stmt->execute(['id' => $comp['id']]);
                $item = $stmt->fetch();

                $needed = $comp['quantity'] * $data['quantity']; // quantity here is Bundle Quantity
                if ($item['quantity'] < $needed) {
                    throw new \Exception("Insufficient stock for item ID: " . $comp['id']);
                }
                $totalCost += $item['cost_price'] * $comp['quantity'];
                
                // 2. Deduct Stock immediately
                $this->adjustStock($comp['id'], -$needed);
            }

            // 3. Create Bundle Item
            // Ensure data includes cost_price if not set (sum of components)
            if (empty($data['cost_price'])) {
                $data['cost_price'] = $totalCost;
            }
            $data['type'] = 'bundle';
            
            // Insert Bundle Item
            // Reuse create method or manual insert? reusing create implies strictly single fields mostly.
            // Let's do manual insert to be safe and explicit with 'type'
            $sql = "INSERT INTO items (name, type, category, sku, unit, price, cost_price, quantity, location, image_path) 
                    VALUES (:name, 'bundle', :category, :sku, :unit, :price, :cost_price, :quantity, :location, :image_path)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'name' => $data['name'],
                'category' => $data['category'],
                'sku' => $data['sku'],
                'unit' => $data['unit'] ?? 'bundle',
                'price' => $data['price'],
                'cost_price' => $data['cost_price'],
                'quantity' => $data['quantity'],
                'location' => $data['location'] ?? '',
                'image_path' => $data['image_path'] ?? ''
            ]);
            $bundleId = $this->pdo->lastInsertId();

            // 4. Link Components
            $stmt = $this->pdo->prepare("INSERT INTO item_bundles (parent_item_id, child_item_id, quantity) VALUES (:pid, :cid, :qty)");
            foreach ($components as $comp) {
                $stmt->execute([
                    'pid' => $bundleId,
                    'cid' => $comp['id'],
                    'qty' => $comp['quantity'] // Qty per bundle unit
                ]);
            }

            $this->pdo->commit();
            return $bundleId;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getBundleComponents($bundleId) {
        $stmt = $this->pdo->prepare("SELECT ib.*, i.name, i.sku FROM item_bundles ib JOIN items i ON ib.child_item_id = i.id WHERE ib.parent_item_id = :id");
        $stmt->execute(['id' => $bundleId]);
        return $stmt->fetchAll();
    }

    public function disassembleBundle($bundleId, $quantityToUngroup) {
        try {
            $this->pdo->beginTransaction();

            // 1. Verify Bundle Stock
            $stmt = $this->pdo->prepare("SELECT quantity FROM items WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $bundleId]);
            $bundle = $stmt->fetch();

            if ($bundle['quantity'] < $quantityToUngroup) {
                throw new \Exception("Insufficient bundle stock to ungroup.");
            }

            // 2. Get Components
            $components = $this->getBundleComponents($bundleId);

            // 3. Restore Stock to Components
            foreach ($components as $comp) {
                $restoreQty = $comp['quantity'] * $quantityToUngroup;
                $this->adjustStock($comp['child_item_id'], $restoreQty);
            }

            // 4. Reduce Bundle Stock
            $this->adjustStock($bundleId, -$quantityToUngroup);

            $this->pdo->commit();
            return true;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
