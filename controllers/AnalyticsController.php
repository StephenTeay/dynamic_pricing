<?php
// controllers/AnalyticsController.php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Analytics.php';
require_once __DIR__ . '/../core/Session.php';

class AnalyticsController {
    private $orderModel;
    private $productModel;
    private $analyticsModel;
    
    public function __construct() {
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->analyticsModel = new Analytics();
    }
    
    /**
     * Get revenue analytics
     */
    public function getRevenueAnalytics($period = 'month') {
        return $this->orderModel->getRevenueStats(Session::getUserId(), $period);
    }
    
    /**
     * Get sales analytics
     */
    public function getSalesAnalytics($days = 30) {
        return $this->analyticsModel->getSalesAnalytics(Session::getUserId(), $days);
    }
    
    /**
     * Get product analytics
     */
    public function getProductAnalytics() {
        return $this->analyticsModel->getProductAnalytics(Session::getUserId());
    }
    
    /**
     * Get order statistics
     */
    public function getOrderStats() {
        return $this->orderModel->getOrderStatsByStatus(Session::getUserId());
    }
    
    /**
     * Get inventory analytics
     */
    public function getInventoryAnalytics() {
        $products = $this->productModel->getProductsWithInventory(Session::getUserId());
        
        $inStock = 0;
        $lowStock = 0;
        $outOfStock = 0;
        
        foreach ($products as $product) {
            if ($product['quantity_available'] == 0) {
                $outOfStock++;
            } elseif ($product['quantity_available'] <= $product['low_stock_threshold']) {
                $lowStock++;
            } else {
                $inStock++;
            }
        }
        
        return [
            'in_stock' => $inStock,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'total' => count($products)
        ];
    }
    
    /**
     * Get trending products
     */
    public function getTrendingProducts() {
        return $this->analyticsModel->getProductAnalytics(Session::getUserId());
    }
}
?>
