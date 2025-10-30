<?php
// services/InventoryService.php

require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../models/Notification.php';

class InventoryService {
    private $inventoryModel;
    private $notificationModel;
    
    public function __construct() {
        $this->inventoryModel = new Inventory();
        $this->notificationModel = new Notification();
    }
    
    /**
     * Check and alert low stock
     */
    public function checkLowStock($productId) {
        $inventory = $this->inventoryModel->getByProductId($productId);
        
        if (!$inventory) {
            return false;
        }
        
        if ($inventory['quantity_available'] <= $inventory['low_stock_threshold']) {
            $this->createLowStockNotification($productId, $inventory);
            return true;
        }
        
        return false;
    }
    
    /**
     * Create low stock notification
     */
    private function createLowStockNotification($productId, $inventory) {
        // Implementation for creating notifications
        // This would be integrated with your notification system
    }
    
    /**
     * Reserve stock for order
     */
    public function reserveStock($productId, $quantity) {
        return $this->inventoryModel->reserveStock($productId, $quantity);
    }
    
    /**
     * Release stock (cancel order)
     */
    public function releaseStock($productId, $quantity) {
        return $this->inventoryModel->releaseReservedStock($productId, $quantity);
    }
    
    /**
     * Confirm stock (complete order)
     */
    public function confirmStock($productId, $quantity) {
        return $this->inventoryModel->confirmSale($productId, $quantity);
    }
    
    /**
     * Get stock status
     */
    public function getStockStatus($productId) {
        return $this->inventoryModel->getStockLevel($productId);
    }
}
?>
