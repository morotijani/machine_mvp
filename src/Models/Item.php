<?php
namespace App\Models;

use PDO;

class Item {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($limit = null, $offset = 0, $search = null, $lowStock = false, $sort = 'created_at', $order = 'DESC') {
        $sql = "SELECT items.*, users.username as created_by_name FROM items LEFT JOIN users ON items.created_by = users.id WHERE items.is_deleted = 0";
        if ($lowStock) {
            $sql .= " AND items.quantity <= 5";
        }
        $params = [];

        if ($search) {
            $sql .= " AND (items.name LIKE :s1 OR items.sku LIKE :s2 OR items.category LIKE :s3)";
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

        $orderField = ($sort === 'name') ? "TRIM(items.name)" : "items." . $sort;
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
        $stmt = $this->pdo->prepare("SELECT items.*, users.username as created_by_name FROM items LEFT JOIN users ON items.created_by = users.id WHERE items.id = :id AND items.is_deleted = 0");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findBySku($sku) {
        $stmt = $this->pdo->prepare("SELECT * FROM items WHERE sku = :sku AND is_deleted = 0");
        $stmt->execute(['sku' => $sku]);
        return $stmt->fetch();
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("UPDATE items SET is_deleted = 1, sync_status = 0 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function create($data) {
        $sql = "INSERT INTO items (name, category, sku, unit, price, cost_price, quantity, location, image_path, created_by)
                VALUES (:name, :category, :sku, :unit, :price, :cost_price, :quantity, :location, :image_path, :created_by)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data) {
        if (array_key_exists('image_path', $data)) {
            $sql = "UPDATE items SET name=:name, category=:category, sku=:sku, unit=:unit,
                    price=:price, cost_price=:cost_price, quantity=:quantity, location=:location, image_path=:image_path, sync_status=0
                    WHERE id=:id";
        } else {
             $sql = "UPDATE items SET name=:name, category=:category, sku=:sku, unit=:unit,
                    price=:price, cost_price=:cost_price, quantity=:quantity, location=:location, sync_status=0
                    WHERE id=:id";
        }
        $data['id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function adjustStock($id, $quantityChange) {
        $sql = "UPDATE items SET quantity = quantity + :change, sync_status = 0 WHERE id = :id";
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

    public function createBundle(&$data, $components) {
        try {
            $this->pdo->beginTransaction();

            // 0. Merge duplicate component rows by item ID
            $mergedComponents = [];
            foreach ($components as $comp) {
                $cid = (int)$comp['id'];
                $qty = (int)$comp['quantity'];
                if ($cid <= 0 || $qty <= 0) continue;
                if (!isset($mergedComponents[$cid])) {
                    $mergedComponents[$cid] = ['id' => $cid, 'quantity' => 0];
                }
                $mergedComponents[$cid]['quantity'] += $qty;
            }
            $components = array_values($mergedComponents);
            if (empty($components)) {
                throw new \Exception("A bundle must have at least one valid component item.");
            }

            // 1. Verify Stock for all components
            $totalCost = 0;
            $bundleQty = (int)($data['quantity'] ?? 0);
            foreach ($components as $comp) {
                $forUpdate = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite' ? "" : "FOR UPDATE";
                $stmt = $this->pdo->prepare("SELECT name, sku, quantity, cost_price FROM items WHERE id = :id $forUpdate");
                $stmt->execute(['id' => $comp['id']]);
                $item = $stmt->fetch();

                if (!$item) {
                    throw new \Exception("Component item ID {$comp['id']} not found.");
                }

                $needed = $comp['quantity'] * $bundleQty; // quantity here is Bundle Quantity
                if ($needed > 0 && $item['quantity'] < $needed) {
                    throw new \Exception("Insufficient stock for item: " . $item['name'] . " (" . $item['sku'] . "). Available: " . $item['quantity'] . ", Needed: " . $needed);
                }
                $totalCost += $item['cost_price'] * $comp['quantity'];

                // 2. Deduct Stock immediately if bundleQty > 0
                if ($needed > 0) {
                    $this->adjustStock($comp['id'], -$needed);

                    // Log allocation
                    $this->addLog([
                        'item_id' => $comp['id'],
                        'user_id' => $_SESSION['user_id'] ?? 1,
                        'action' => 'bundle_allocation',
                        'details' => "Allocated {$needed} units to bundle '{$data['name']}'",
                        'old_quantity' => $item['quantity'],
                        'new_quantity' => $item['quantity'] - $needed
                    ]);
                }
            }

            // 3. Create Bundle Item
            $data['cost_price'] = $totalCost;
            $data['type'] = 'bundle';

            // Insert Bundle Item
            $sql = "INSERT INTO items (name, type, category, sku, unit, price, cost_price, quantity, location, image_path, created_by)
                    VALUES (:name, :type, :category, :sku, :unit, :price, :cost_price, :quantity, :location, :image_path, :created_by)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'name' => $data['name'],
                'type' => 'bundle',
                'category' => $data['category'],
                'sku' => $data['sku'],
                'unit' => $data['unit'] ?? 'bundle',
                'price' => $data['price'],
                'cost_price' => $data['cost_price'],
                'quantity' => $bundleQty,
                'location' => $data['location'] ?? '',
                'image_path' => $data['image_path'] ?? '',
                'created_by' => $data['created_by'] ?? null
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

            // 1. Verify Bundle Stock (include name for audit log)
            $forUpdate = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite' ? "" : "FOR UPDATE";
            $stmt = $this->pdo->prepare("SELECT name, quantity FROM items WHERE id = :id $forUpdate");
            $stmt->execute(['id' => $bundleId]);
            $bundle = $stmt->fetch();

            if (!$bundle || $bundle['quantity'] < $quantityToUngroup) {
                throw new \Exception("Insufficient bundle stock to ungroup.");
            }

            // 2. Get Components (summed by child_item_id in case of legacy duplicate rows)
            $components = $this->getBundleComponents($bundleId);
            $merged = [];
            foreach ($components as $comp) {
                $cid = (int)$comp['child_item_id'];
                $merged[$cid] = ($merged[$cid] ?? 0) + (int)$comp['quantity'];
            }

            // 3. Restore Stock to Components
            foreach ($merged as $childId => $compQty) {
                $restoreQty = $compQty * $quantityToUngroup;
                if ($restoreQty <= 0) continue;

                $stmtQ = $this->pdo->prepare("SELECT quantity FROM items WHERE id = :id");
                $stmtQ->execute(['id' => $childId]);
                $oldQty = (int)$stmtQ->fetchColumn();

                $this->adjustStock($childId, $restoreQty);

                $this->addLog([
                    'item_id' => $childId,
                    'user_id' => $_SESSION['user_id'] ?? 1,
                    'action' => 'bundle_disassembly',
                    'details' => "Restored {$restoreQty} units from disassembled bundle '{$bundle['name']}'",
                    'old_quantity' => $oldQty,
                    'new_quantity' => $oldQty + $restoreQty
                ]);
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

    public function updateBundle($id, &$data, $newComponents) {
        try {
            $this->pdo->beginTransaction();

            // 0. Merge duplicate component rows in $newComponents by item ID
            $mergedNew = [];
            foreach ($newComponents as $nc) {
                $cid = (int)$nc['id'];
                $qty = (int)$nc['quantity'];
                if ($cid <= 0 || $qty <= 0) continue;
                if (!isset($mergedNew[$cid])) {
                    $mergedNew[$cid] = ['id' => $cid, 'quantity' => 0];
                }
                $mergedNew[$cid]['quantity'] += $qty;
            }
            $newComponents = array_values($mergedNew);
            if (empty($newComponents)) {
                throw new \Exception("A bundle must have at least one valid component item.");
            }

            // 1. Fetch Current State
            $forUpdate = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite' ? "" : "FOR UPDATE";
            $stmt = $this->pdo->prepare("SELECT name, quantity FROM items WHERE id = :id $forUpdate");
            $stmt->execute(['id' => $id]);
            $currentBundle = $stmt->fetch();
            $currentBundleQty = (int)$currentBundle['quantity'];
            $bundleName = $data['name'] ?? $currentBundle['name'];

            $newBundleQty = isset($data['quantity']) ? (int)$data['quantity'] : $currentBundleQty;

            // 2. Fetch Old Components (summed by child_item_id in case of legacy duplicates)
            $oldComponents = $this->getBundleComponents($id);
            $oldMap = [];
            foreach ($oldComponents as $oc) {
                $cid = (int)$oc['child_item_id'];
                $oldMap[$cid] = ($oldMap[$cid] ?? 0) + (int)$oc['quantity'];
            }

            $processedItemIds = [];

            // A. Process New Components
            $newTotalCost = 0;
            foreach ($newComponents as $nc) {
                $childId = (int)$nc['id'];
                $newRecipeQty = (int)$nc['quantity'];

                $oldRecipeQty = $oldMap[$childId] ?? 0;

                $oldTotalUsed = $currentBundleQty * $oldRecipeQty;
                $newTotalNeeded = $newBundleQty * $newRecipeQty;

                $netChange = $newTotalNeeded - $oldTotalUsed;

                $stmt = $this->pdo->prepare("SELECT name, sku, quantity, cost_price FROM items WHERE id = :id $forUpdate");
                $stmt->execute(['id' => $childId]);
                $childItem = $stmt->fetch();
                if (!$childItem) {
                    throw new \Exception("Component item ID: $childId not found.");
                }

                // Validate and Apply
                if ($netChange > 0) {
                    if ($childItem['quantity'] < $netChange) {
                        throw new \Exception("Insufficient stock for item: {$childItem['name']} ({$childItem['sku']}). Need {$netChange} more, available: {$childItem['quantity']}.");
                    }
                    $oldQty = (int)$childItem['quantity'];
                    $this->adjustStock($childId, -$netChange);

                    $this->addLog([
                        'item_id' => $childId,
                        'user_id' => $_SESSION['user_id'] ?? 1,
                        'action' => 'bundle_allocation',
                        'details' => "Allocated additional {$netChange} units to updated bundle '{$bundleName}'",
                        'old_quantity' => $oldQty,
                        'new_quantity' => $oldQty - $netChange
                    ]);
                } elseif ($netChange < 0) {
                    $restoreAmount = abs($netChange);
                    $oldQty = (int)$childItem['quantity'];

                    $this->adjustStock($childId, $restoreAmount);

                    $this->addLog([
                        'item_id' => $childId,
                        'user_id' => $_SESSION['user_id'] ?? 1,
                        'action' => 'bundle_disassembly',
                        'details' => "Restored {$restoreAmount} units from updated bundle '{$bundleName}'",
                        'old_quantity' => $oldQty,
                        'new_quantity' => $oldQty + $restoreAmount
                    ]);
                }

                $newTotalCost += $childItem['cost_price'] * $newRecipeQty;
                $processedItemIds[] = $childId;
            }

            // B. Process Removed Components (In Old but not in New)
            foreach ($oldMap as $childId => $oldRecipeQty) {
                if (!in_array($childId, $processedItemIds)) {
                    $oldTotalUsed = $currentBundleQty * $oldRecipeQty;
                    if ($oldTotalUsed > 0) {
                        $stmtQ = $this->pdo->prepare("SELECT quantity FROM items WHERE id = :id");
                        $stmtQ->execute(['id' => $childId]);
                        $oldQty = (int)$stmtQ->fetchColumn();

                        $this->adjustStock($childId, $oldTotalUsed);

                        $this->addLog([
                            'item_id' => $childId,
                            'user_id' => $_SESSION['user_id'] ?? 1,
                            'action' => 'bundle_disassembly',
                            'details' => "Restored {$oldTotalUsed} units (removed from updated bundle '{$bundleName}')",
                            'old_quantity' => $oldQty,
                            'new_quantity' => $oldQty + $oldTotalUsed
                        ]);
                    }
                }
            }

            // 4. Update Bundle Item Details (always set calculated cost_price)
            $data['cost_price'] = $newTotalCost;

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

    public function updateParentBundlePrices($childItemId, $oldChildPrice = null, $newChildPrice = null) {
        $stmt = $this->pdo->prepare("SELECT DISTINCT parent_item_id FROM item_bundles WHERE child_item_id = :cid");
        $stmt->execute(['cid' => $childItemId]);
        $parentIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($parentIds as $parentId) {
            $stmtParent = $this->pdo->prepare("SELECT price, cost_price FROM items WHERE id = :id");
            $stmtParent->execute(['id' => $parentId]);
            $parentData = $stmtParent->fetch(PDO::FETCH_ASSOC);
            if (!$parentData) continue;

            $components = $this->getBundleComponents($parentId);
            $newAutoPrice = 0;
            $newCost = 0;
            $childQtyInBundle = 0;

            foreach ($components as $comp) {
                $stmtPrice = $this->pdo->prepare("SELECT price, cost_price FROM items WHERE id = :id");
                $stmtPrice->execute(['id' => $comp['child_item_id']]);
                $priceData = $stmtPrice->fetch(PDO::FETCH_ASSOC);

                if ($priceData) {
                    $newAutoPrice += $priceData['price'] * $comp['quantity'];
                    $newCost += $priceData['cost_price'] * $comp['quantity'];
                }
                if ((int)$comp['child_item_id'] === (int)$childItemId) {
                    $childQtyInBundle += (int)$comp['quantity'];
                }
            }

            // Preserve custom bundle selling price if the bundle price was manually customized
            $targetBundlePrice = $newAutoPrice;
            if ($oldChildPrice !== null && $newChildPrice !== null) {
                $priceDeltaPerUnit = (float)$newChildPrice - (float)$oldChildPrice;
                $oldAutoPrice = $newAutoPrice - ($priceDeltaPerUnit * $childQtyInBundle);
                $currentBundlePrice = (float)$parentData['price'];

                if (abs($currentBundlePrice - $oldAutoPrice) > 0.01) {
                    // Parent bundle had a custom selling price: adjust only by the component price delta
                    $targetBundlePrice = max(0, $currentBundlePrice + ($priceDeltaPerUnit * $childQtyInBundle));
                }
            }

            $stmtUpdate = $this->pdo->prepare("UPDATE items SET price = :price, cost_price = :cost, sync_status = 0 WHERE id = :id");
            $stmtUpdate->execute([
                'price' => $targetBundlePrice,
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
            $stmt = $this->pdo->prepare("DELETE FROM item_bundles WHERE parent_item_id = :pid OR child_item_id = :cid");
            $stmt->execute(['pid' => $id, 'cid' => $id]);

            $stmt = $this->pdo->prepare("DELETE FROM items WHERE id = :id");
            $stmt->execute(['id' => $id]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function hasSaleBundleItemsTable() {
        try {
            $this->pdo->query("SELECT 1 FROM sale_bundle_items LIMIT 1");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getSalesHistoryCount($itemId) {
        if ($this->hasSaleBundleItemsTable()) {
            $sql = "
                SELECT COUNT(*) FROM (
                    SELECT si.sale_id FROM sale_items si
                    JOIN sales s ON si.sale_id = s.id
                    WHERE si.item_id = :iid AND si.quantity > 0 AND s.voided = 0
                    UNION ALL
                    SELECT si.sale_id FROM sale_items si
                    JOIN sales s ON si.sale_id = s.id
                    JOIN sale_bundle_items sbi ON si.id = sbi.sale_item_id
                    WHERE sbi.child_item_id = :ciid1 AND si.quantity > 0 AND s.voided = 0
                    UNION ALL
                    SELECT si.sale_id FROM sale_items si
                    JOIN sales s ON si.sale_id = s.id
                    JOIN item_bundles ib ON si.item_id = ib.parent_item_id
                    WHERE ib.child_item_id = :ciid2 AND si.quantity > 0 AND s.voided = 0
                      AND NOT EXISTS (SELECT 1 FROM sale_bundle_items sbi2 WHERE sbi2.sale_item_id = si.id)
                ) as combined
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['iid' => $itemId, 'ciid1' => $itemId, 'ciid2' => $itemId]);
            return $stmt->fetchColumn() ?: 0;
        }

        $sql = "
            SELECT COUNT(*) FROM (
                SELECT si.sale_id FROM sale_items si
                JOIN sales s ON si.sale_id = s.id
                WHERE si.item_id = :iid AND si.quantity > 0 AND s.voided = 0
                UNION ALL
                SELECT si.sale_id FROM sale_items si
                JOIN sales s ON si.sale_id = s.id
                JOIN item_bundles ib ON si.item_id = ib.parent_item_id
                WHERE ib.child_item_id = :ciid AND si.quantity > 0 AND s.voided = 0
            ) as combined
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['iid' => $itemId, 'ciid' => $itemId]);
        return $stmt->fetchColumn() ?: 0;
    }

    public function getSalesHistoryPaginated($itemId, $limit, $offset) {
        if ($this->hasSaleBundleItemsTable()) {
            $sql = "
                SELECT * FROM (
                    SELECT
                        si.sale_id,
                        si.quantity,
                        si.price_at_sale,
                        si.subtotal,
                        s.created_at,
                        s.payment_status,
                        c.name as customer_name,
                        c.id as customer_id,
                        u.username as seller_name,
                        NULL as bundle_name
                    FROM sale_items si
                    JOIN sales s ON si.sale_id = s.id
                    LEFT JOIN customers c ON s.customer_id = c.id
                    LEFT JOIN users u ON s.user_id = u.id
                    WHERE si.item_id = :iid AND si.quantity > 0 AND s.voided = 0

                    UNION ALL

                    SELECT
                        si.sale_id,
                        (si.quantity * sbi.quantity) as quantity,
                        0 as price_at_sale,
                        0 as subtotal,
                        s.created_at,
                        s.payment_status,
                        c.name as customer_name,
                        c.id as customer_id,
                        u.username as seller_name,
                        i_bundle.name as bundle_name
                    FROM sale_items si
                    JOIN sales s ON si.sale_id = s.id
                    JOIN sale_bundle_items sbi ON si.id = sbi.sale_item_id
                    JOIN items i_bundle ON sbi.bundle_item_id = i_bundle.id
                    LEFT JOIN customers c ON s.customer_id = c.id
                    LEFT JOIN users u ON s.user_id = u.id
                    WHERE sbi.child_item_id = :ciid1 AND si.quantity > 0 AND s.voided = 0

                    UNION ALL

                    SELECT
                        si.sale_id,
                        (si.quantity * ib.quantity) as quantity,
                        0 as price_at_sale,
                        0 as subtotal,
                        s.created_at,
                        s.payment_status,
                        c.name as customer_name,
                        c.id as customer_id,
                        u.username as seller_name,
                        i_bundle.name as bundle_name
                    FROM sale_items si
                    JOIN sales s ON si.sale_id = s.id
                    JOIN item_bundles ib ON si.item_id = ib.parent_item_id
                    JOIN items i_bundle ON ib.parent_item_id = i_bundle.id
                    LEFT JOIN customers c ON s.customer_id = c.id
                    LEFT JOIN users u ON s.user_id = u.id
                    WHERE ib.child_item_id = :ciid2 AND si.quantity > 0 AND s.voided = 0
                      AND NOT EXISTS (SELECT 1 FROM sale_bundle_items sbi2 WHERE sbi2.sale_item_id = si.id)
                ) as combined
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':iid', $itemId, \PDO::PARAM_INT);
            $stmt->bindValue(':ciid1', $itemId, \PDO::PARAM_INT);
            $stmt->bindValue(':ciid2', $itemId, \PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        $sql = "
            SELECT * FROM (
                SELECT
                    si.sale_id,
                    si.quantity,
                    si.price_at_sale,
                    si.subtotal,
                    s.created_at,
                    s.payment_status,
                    c.name as customer_name,
                    c.id as customer_id,
                    u.username as seller_name,
                    NULL as bundle_name
                FROM sale_items si
                JOIN sales s ON si.sale_id = s.id
                LEFT JOIN customers c ON s.customer_id = c.id
                LEFT JOIN users u ON s.user_id = u.id
                WHERE si.item_id = :iid AND si.quantity > 0 AND s.voided = 0

                UNION ALL

                SELECT
                    si.sale_id,
                    (si.quantity * ib.quantity) as quantity,
                    0 as price_at_sale,
                    0 as subtotal,
                    s.created_at,
                    s.payment_status,
                    c.name as customer_name,
                    c.id as customer_id,
                    u.username as seller_name,
                    i_bundle.name as bundle_name
                FROM sale_items si
                JOIN sales s ON si.sale_id = s.id
                JOIN item_bundles ib ON si.item_id = ib.parent_item_id
                JOIN items i_bundle ON ib.parent_item_id = i_bundle.id
                LEFT JOIN customers c ON s.customer_id = c.id
                LEFT JOIN users u ON s.user_id = u.id
                WHERE ib.child_item_id = :ciid AND si.quantity > 0 AND s.voided = 0
            ) as combined
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':iid', $itemId, \PDO::PARAM_INT);
        $stmt->bindValue(':ciid', $itemId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addLog($data) {
        $sql = "INSERT INTO item_logs (item_id, user_id, action, details, old_quantity, new_quantity)
                VALUES (:item_id, :user_id, :action, :details, :old_quantity, :new_quantity)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'item_id' => $data['item_id'],
            'user_id' => $data['user_id'],
            'action' => $data['action'],
            'details' => $data['details'] ?? null,
            'old_quantity' => $data['old_quantity'] ?? null,
            'new_quantity' => $data['new_quantity'] ?? null
        ]);
    }

    public function getLogsCount($itemId) {
        $sql = "SELECT COUNT(*) FROM item_logs WHERE item_id = :iid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['iid' => $itemId]);
        return $stmt->fetchColumn() ?: 0;
    }

    public function getLogsPaginated($itemId, $limit, $offset) {
        $sql = "SELECT l.*, u.username as operator_name
                FROM item_logs l
                LEFT JOIN users u ON l.user_id = u.id
                WHERE l.item_id = :iid
                ORDER BY l.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':iid', $itemId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getParentBundles($childItemId) {
        $stmt = $this->pdo->prepare("
            SELECT i.*, ib.quantity as qty_required
            FROM items i
            JOIN item_bundles ib ON i.id = ib.parent_item_id
            WHERE ib.child_item_id = :cid AND i.is_deleted = 0
        ");
        $stmt->execute(['cid' => $childItemId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
