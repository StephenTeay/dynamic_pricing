<?php
// models/Inventory.php

require_once __DIR__ . '/../core/Model.php';

class Inventory extends Model {
    protected $table = 'inventory';
    protected $primaryKey = 'inventory_id';
    
    public function getByProductId($productId) {
        return $this->findOne(['product_id' => $productId]);
    }
    
    public function updateStock($productId, $quantity) {
        $inventory = $this->getByProductId($productId);
        
        if ($inventory) {
            return $this->update($inventory['inventory_id'], [
                'quantity_available' => $quantity,
                'last_restocked' => date('Y-m-d H:i:s')
            ]);
        }
        
        return false;
    }
    
    public function adjustStock($productId, $adjustment) {
        $inventory = $this->getByProductId($productId);
        
        if ($inventory) {
            $newQuantity = max(0, $inventory['quantity_available'] + $adjustment);
            return $this->update($inventory['inventory_id'], [
                'quantity_available' => $newQuantity
            ]);
        }
        
        return false;
    }
    
    public function reserveStock($productId, $quantity) {
        $inventory = $this->getByProductId($productId);
        
        if (!$inventory || $inventory['quantity_available'] < $quantity) {
            return false;
        }
        
        return $this->update($inventory['inventory_id'], [
            'quantity_available' => $inventory['quantity_available'] - $quantity,
            'quantity_reserved' => $inventory['quantity_reserved'] + $quantity
        ]);
    }
    
    public function releaseReservedStock($productId, $quantity) {
        $inventory = $this->getByProductId($productId);
        
        if (!$inventory) {
            return false;
        }
        
        return $this->update($inventory['inventory_id'], [
            'quantity_available' => $inventory['quantity_available'] + $quantity,
            'quantity_reserved' => max(0, $inventory['quantity_reserved'] - $quantity)
        ]);
    }
    
    public function confirmSale($productId, $quantity) {
        $inventory = $this->getByProductId($productId);
        
        if (!$inventory || $inventory['quantity_reserved'] < $quantity) {
            return false;
        }
        
        return $this->update($inventory['inventory_id'], [
            'quantity_reserved' => $inventory['quantity_reserved'] - $quantity
        ]);
    }
    
    public function isAvailable($productId, $quantity) {
        $inventory = $this->getByProductId($productId);
        return $inventory && $inventory['quantity_available'] >= $quantity;
    }
    
    public function getStockLevel($productId) {
        $inventory = $this->getByProductId($productId);
        
        if (!$inventory) {
            return 'out_of_stock';
        }
        
        $available = $inventory['quantity_available'];
        $lowThreshold = $inventory['low_stock_threshold'];
        
        if ($available == 0) {
            return 'out_of_stock';
        } elseif ($available <= $lowThreshold) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }
    
    public function createInventoryForProduct($productId, $initialQuantity = 0) {
        return $this->create([
            'product_id' => $productId,
            'quantity_available' => $initialQuantity,
            'quantity_reserved' => 0,
            'reorder_point' => 10,
            'low_stock_threshold' => 20,
            'high_stock_threshold' => 100
        ]);
    }
}