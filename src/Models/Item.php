<?php
namespace App\Models;

use PDO;

class Item {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($limit = null, $offset = 0, $search = null, $lowStock = false, $sort = 'created_at', $order = 'DESC') {
        $sql = "SELECT * FROM items WHERE is_deleted = 0";
        if ($lowStock) {
            $sql .= " AND quantity <= 5";
        }
        $params = [];
        
        if ($search) {
            $sql .= " AND (name LIKE :s1 OR sku LIKE :s2 OR category LIKE :s3)";
            $params['s1'] = "%$search%";
            $params['s2'] = "%$search%";
            $params['s3'] = "%$search%";
        }
        
        // Whitelist for sorting
        $allowedSort = ['name', 'price', 'quantity', 'created_at', 'category', 'sku', 'location'];
        $allowedOrder = ['ASC', 'DESC'];
        
        $sort = strtolower($sort);
        if (!in_array($sort, $allowedSort)) $sort = 'created_at';
        if (!in_array(strtoupper($order), $allowedOrder)) $order = 'DESC';
        
        $orderField = ($sort === 'name') ? "TRIM(name)" : $sort;
        $sql .= " ORDER BY $orderField $order";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->pdo->prepare($sql);
        if ($search) {
            $stmt->bindValue(':s1', $params['s1']);
            $stmt->bindValue(':s2', $params['s2']);
            $stmt->bindValue(':s3', $params['s3']);
        }
        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll($search = null, $lowStock = false) {
        $sql = "SELECT COUNT(*) FROM items WHERE is_deleted = 0";
        if ($lowStock) {
            $sql .= " AND quantity <= 5";
        }
        $params = [];
        
        if ($search) {
            $sql .= " AND (name LIKE :s1 OR sku LIKE :s2 OR category LIKE :s3)";
            $params['s1'] = "%$search%";
            $params['s2'] = "%$search%";
            $params['s3'] = "%$search%";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM items WHERE id = :id AND is_deleted = 0");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findBySku($sku) {
        $stmt = $this->pdo->prepare("SELECT * FROM items WHERE sku = :sku AND is_deleted = 0");
        $stmt->execute(['sku' => $sku]);
        return $stmt->fetch();
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("UPDATE items SET is_deleted = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
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
    
    public function adjustStock($id, $quantityChange) {
        $sql = "UPDATE items SET quantity = quantity + :change WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['change' => $quantityChange, 'id' => $id]);
    }

    /**
     * Check if an SKU already exists in the database
     */
    public function isSkuExists($sku, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM items WHERE sku = :sku AND is_deleted = 0";
        $params = ['sku' => $sku];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Generate a guaranteed unique SKU
     */
    public function generateUniqueSKU($prefix = 'SKU') {
        $unique = false;
        $sku = '';
        
        while (!$unique) {
            // Generate a random 8-character string after the prefix
            $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            $sku = $prefix . "-" . $random;
            
            if (!$this->isSkuExists($sku)) {
                $unique = true;
            }
        }
        
        return $sku;
    }

    public function createBundle($data, $components) {
        try {
            $this->pdo->beginTransaction();

            // 1. Verify Stock for all components
            $totalCost = 0;
            foreach ($components as $comp) {
                $stmt = $this->pdo->prepare("SELECT name, sku, quantity, cost_price FROM items WHERE id = :id FOR UPDATE");
                $stmt->execute(['id' => $comp['id']]);
                $item = $stmt->fetch();

                $needed = $comp['quantity'] * $data['quantity']; // quantity here is Bundle Quantity
                if ($item['quantity'] < $needed) {
                    throw new \Exception("Insufficient stock for item: " . $item['name'] . " (" . $item['sku'] . "). Available: " . $item['quantity'] . ", Needed: " . $needed);
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

    public function updateBundle($id, $data, $newComponents) {
        try {
            $this->pdo->beginTransaction();

            // 1. Fetch Current State
            $stmt = $this->pdo->prepare("SELECT quantity FROM items WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $id]);
            $currentBundle = $stmt->fetch();
            $currentBundleQty = $currentBundle['quantity'];

            // If updating bundle quantity (e.g. user manually changed stock field, though usually disabled)
            // But requirement says "edit bundle stock quantity".
            // Let's assume $data['quantity'] determines the NEW target quantity for the bundle.
            $newBundleQty = isset($data['quantity']) ? $data['quantity'] : $currentBundleQty;

            // 2. Fetch Old Components
            $oldComponents = $this->getBundleComponents($id);
            // Map old components for easy lookup: [itemId => qtyPerBundle]
            $oldMap = [];
            foreach ($oldComponents as $oc) {
                $oldMap[$oc['child_item_id']] = $oc['quantity'];
            }

            // 3. Calculate Stock Changes (The "Net Effect")
            // Concept:
            // Old Total Used = OldBundleQty * OldRecipeQty
            // New Total Needed = NewBundleQty * NewRecipeQty
            // Net Change = New Total Needed - Old Total Used
            // If Net Change > 0: Deduct from stock (Verify availability first)
            // If Net Change < 0: Add back to stock

            $processedItemIds = [];

            // A. Process New Components
            $newTotalCost = 0;
            foreach ($newComponents as $nc) {
                $childId = $nc['id'];
                $newRecipeQty = $nc['quantity'];
                
                $oldRecipeQty = isset($oldMap[$childId]) ? $oldMap[$childId] : 0;
                
                $oldTotalUsed = $currentBundleQty * $oldRecipeQty;
                $newTotalNeeded = $newBundleQty * $newRecipeQty;
                
                $netChange = $newTotalNeeded - $oldTotalUsed;

                // Validate and Apply
                if ($netChange > 0) {
                    // Need more. Check stock.
                    $stmt = $this->pdo->prepare("SELECT quantity, cost_price FROM items WHERE id = :id");
                    $stmt->execute(['id' => $childId]);
                    $childItem = $stmt->fetch();

                    if ($childItem['quantity'] < $netChange) {
                        throw new \Exception("Insufficient stock for item ID: $childId. Need " . $netChange . " more.");
                    }
                    $this->adjustStock($childId, -$netChange);
                    $newTotalCost += $childItem['cost_price'] * $newRecipeQty;
                } elseif ($netChange < 0) {
                    // Returning stock.
                    // Note: abs($netChange) is the amount to add back.
                    $this->adjustStock($childId, abs($netChange));
                    // Cost calculation still based on new recipe
                    $stmt = $this->pdo->prepare("SELECT cost_price FROM items WHERE id = :id");
                    $stmt->execute(['id' => $childId]);
                    $childItem = $stmt->fetch();
                    $newTotalCost += $childItem['cost_price'] * $newRecipeQty;
                } else {
                    // No change in quantity, but need cost for total
                    $stmt = $this->pdo->prepare("SELECT cost_price FROM items WHERE id = :id");
                    $stmt->execute(['id' => $childId]);
                    $childItem = $stmt->fetch();
                    $newTotalCost += $childItem['cost_price'] * $newRecipeQty;
                }

                $processedItemIds[] = $childId;
            }

            // B. Process Removed Components (In Old but not in New)
            foreach ($oldMap as $childId => $oldRecipeQty) {
                if (!in_array($childId, $processedItemIds)) {
                    // This item was removed from the bundle entirely.
                    // Return all used stock.
                    $oldTotalUsed = $currentBundleQty * $oldRecipeQty;
                    $this->adjustStock($childId, $oldTotalUsed);
                }
            }

            // 4. Update Bundle Item Details
            if (empty($data['cost_price'])) {
                $data['cost_price'] = $newTotalCost;
            }
            
            // Allow updating quantity here since we handled the stock implications above
            $this->update($id, $data);

            // 5. Update Relations (Replace all)
            $stmt = $this->pdo->prepare("DELETE FROM item_bundles WHERE parent_item_id = :id");
            $stmt->execute(['id' => $id]);

            $stmt = $this->pdo->prepare("INSERT INTO item_bundles (parent_item_id, child_item_id, quantity) VALUES (:pid, :cid, :qty)");
            foreach ($newComponents as $nc) {
                $stmt->execute([
                    'pid' => $id,
                    'cid' => $nc['id'],
                    'qty' => $nc['quantity']
                ]);
            }

            $this->pdo->commit();
            return true;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    public function updateParentBundlePrices($childItemId) {
        $stmt = $this->pdo->prepare("SELECT DISTINCT parent_item_id FROM item_bundles WHERE child_item_id = :cid");
        $stmt->execute(['cid' => $childItemId]);
        $parentIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($parentIds as $parentId) {
            $components = $this->getBundleComponents($parentId);
            $newPrice = 0;
            $newCost = 0;
            
            foreach ($components as $comp) {
                $stmtPrice = $this->pdo->prepare("SELECT price, cost_price FROM items WHERE id = :id");
                $stmtPrice->execute(['id' => $comp['child_item_id']]);
                $priceData = $stmtPrice->fetch(PDO::FETCH_ASSOC);
                
                if ($priceData) {
                    $newPrice += $priceData['price'] * $comp['quantity'];
                    $newCost += $priceData['cost_price'] * $comp['quantity'];
                }
            }
            
            $stmtUpdate = $this->pdo->prepare("UPDATE items SET price = :price, cost_price = :cost WHERE id = :id");
            $stmtUpdate->execute([
                'price' => $newPrice,
                'cost' => $newCost,
                'id' => $parentId
            ]);
        }
    }

    public function getDeleted() {
        $stmt = $this->pdo->query("SELECT * FROM items WHERE is_deleted = 1 ORDER BY updated_at DESC");
        return $stmt->fetchAll();
    }

    public function restore($id) {
        $stmt = $this->pdo->prepare("UPDATE items SET is_deleted = 0 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function hardDelete($id) {
        $this->pdo->beginTransaction();
        try {
            // Block if item has sale history
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM sale_items WHERE item_id = :id");
            $stmt->execute(['id' => $id]);
            if ($stmt->fetchColumn() > 0) {
                throw new \Exception("Cannot delete item with transaction history. Keep it in the bin or restore it.");
            }

            // Delete relations first
            $stmt = $this->pdo->prepare("DELETE FROM item_bundles WHERE parent_item_id = :id OR child_item_id = :id");
            $stmt->execute(['id' => $id]);
            
            $stmt = $this->pdo->prepare("DELETE FROM items WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
