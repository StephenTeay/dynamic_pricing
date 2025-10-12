<?php
// controllers/SellerController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/SellerProfile.php';
require_once __DIR__ . '/../core/Session.php';

class SellerController {
    private $productModel;
    private $orderModel;
    private $sellerProfileModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->sellerProfileModel = new SellerProfile();
    }
    
    /**
     * Get seller profile
     */
    public function getProfile() {
        return $this->sellerProfileModel->getByUserId(Session::getUserId());
    }
    
    /**
     * Update seller profile
     */
    public function updateProfile($data) {
        $profile = $this->getProfile();
        
        if (!$profile) {
            return false;
        }
        
        return $this->sellerProfileModel->update($profile['seller_id'], $data);
    }
    
    /**
     * Get dashboard stats
     */
    public function getDashboardStats() {
        $sellerId = $this->getSellerId();
        
        return [
            'total_products' => $this->productModel->count(['seller_id' => $sellerId, 'is_active' => 1]),
            'low_stock_count' => count($this->productModel->getLowStockProducts($sellerId)),
            'revenue_stats' => $this->orderModel->getRevenueStats($sellerId),
            'order_stats' => $this->orderModel->getOrderStatsByStatus($sellerId)
        ];
    }
    
    /**
     * Get seller's products
     */
    public function getProducts() {
        return $this->productModel->getProductsWithInventory($this->getSellerId());
    }
    
    /**
     * Get seller's orders
     */
    public function getOrders() {
        return $this->orderModel->getOrdersBySeller($this->getSellerId());
    }
    
    /**
     * Get low stock products
     */
    public function getLowStockProducts() {
        return $this->productModel->getLowStockProducts($this->getSellerId());
    }
    
    /**
     * Get seller ID
     */
    private function getSellerId() {
        $profile = $this->getProfile();
        return $profile['seller_id'] ?? null;
    }
}
?>
