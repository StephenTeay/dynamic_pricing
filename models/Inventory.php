<?php
// models/Inventory.php

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../services/PricingEngine.php';
require_once __DIR__ . '/../utils/logger.php';

class Inventory extends Model {
    protected $table = 'inventory';
    protected $primaryKey = 'inventory_id';
    
    private function getPricingEngine() {
        static $engine = null;
        if ($engine === null) {
            $engine = new PricingEngine();
        }
        return $engine;
    }
    
    public function getByProductId($productId) {
        return $this->findOne(['product_id' => $productId]);
    }
    
    public function updateStock($productId, $quantity) {
        try {
            $this->db->beginTransaction();
            
            $inventory = $this->getByProductId($productId);
            
            if ($inventory) {
                $oldQuantity = $inventory['quantity_available'];
                
                $result = $this->update($inventory['inventory_id'], [
                    'quantity_available' => $quantity,
                    'last_restocked' => date('Y-m-d H:i:s')
                ]);
                
                if ($result) {
                    // Check if we need to update price based on new quantity
                    $this->checkAndUpdatePrice($productId, $oldQuantity, $quantity);
                }
                
                $this->db->commit();
                
                // Trigger price update if needed
                $this->checkAndUpdatePrice($productId, $oldQuantity, $quantity);
                
                return $result;
            }
            
            $this->db->rollBack();
            return false;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            Logger::error("Failed to update stock: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function checkAndUpdatePrice($productId, $oldQuantity, $newQuantity) {
        try {
            // Get current inventory thresholds
            $inventory = $this->getByProductId($productId);
            if (!$inventory) return;
            
            $wasLowStock = $oldQuantity < $inventory['low_stock_threshold'];
            $isLowStock = $newQuantity < $inventory['low_stock_threshold'];
            
            // Only trigger price update if stock status changed or quantity dropped further when already low
            if (($wasLowStock !== $isLowStock) || ($isLowStock && $newQuantity < $oldQuantity)) {
                Logger::info(
                    "Stock level change triggered price check for product {$productId}: " .
                    "Old qty: {$oldQuantity}, New qty: {$newQuantity}, " .
                    "Threshold: {$inventory['low_stock_threshold']}"
                );
                
                // Calculate and update new price
                $engine = $this->getPricingEngine();
                $engine->calculateOptimalPrice($productId);
            }
        } catch (Exception $e) {
            Logger::error("Failed to check/update price: " . $e->getMessage());
            // Don't throw - we don't want inventory update to fail if price update fails
        }
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