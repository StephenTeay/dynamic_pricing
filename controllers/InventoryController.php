<?php
// controllers/InventoryController.php

require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../core/Session.php';

class InventoryController {
    private $inventoryModel;
    private $productModel;
    
    public function __construct() {
        $this->inventoryModel = new Inventory();
        $this->productModel = new Product();
    }
    
    /**
     * Get inventory for product
     */
    public function getInventory($productId) {
        $inventory = $this->inventoryModel->getByProductId($productId);
        
        if (!$inventory) {
            return null;
        }
        
        return $inventory;
    }
    
    /**
     * Update inventory stock
     */
    public function updateStock($productId, $quantity) {
        $product = $this->productModel->find($productId);
        
        if (!$product) {
            return false;
        }
        
        // Verify seller ownership
        if ($product['seller_id'] != Session::getUserId()) {
            return false;
        }
        
        return $this->inventoryModel->updateStock($productId, $quantity);
    }
    
    /**
     * Adjust stock
     */
    public function adjustStock($productId, $adjustment) {
        $product = $this->productModel->find($productId);
        
        if (!$product) {
            return false;
        }
        
        if ($product['seller_id'] != Session::getUserId()) {
            return false;
        }
        
        return $this->inventoryModel->adjustStock($productId, $adjustment);
    }
    
    /**
     * Check stock availability
     */
    public function isAvailable($productId, $quantity) {
        return $this->inventoryModel->isAvailable($productId, $quantity);
    }
    
    /**
     * Get stock level
     */
    public function getStockLevel($productId) {
        return $this->inventoryModel->getStockLevel($productId);
    }
    
    /**
     * Reserve stock for order
     */
    public function reserveStock($productId, $quantity) {
        return $this->inventoryModel->reserveStock($productId, $quantity);
    }
    
    /**
     * Release reserved stock
     */
    public function releaseReservedStock($productId, $quantity) {
        return $this->inventoryModel->releaseReservedStock($productId, $quantity);
    }
}
?>
