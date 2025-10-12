<?php
// services/AnalyticsService.php

require_once __DIR__ . '/../models/Analytics.php';

class AnalyticsService {
    private $analyticsModel;
    
    public function __construct() {
        $this->analyticsModel = new Analytics();
    }
    
    /**
     * Get sales analytics
     */
    public function getSalesAnalytics($sellerId, $days = 30) {
        return $this->analyticsModel->getSalesAnalytics($sellerId, $days);
    }
    
    /**
     * Get product analytics
     */
    public function getProductAnalytics($sellerId) {
        return $this->analyticsModel->getProductAnalytics($sellerId);
    }
    
    /**
     * Generate sales report
     */
    public function generateSalesReport($sellerId, $startDate, $endDate) {
        // Implementation for generating sales report
        return [];
    }
    
    /**
     * Get top products
     */
    public function getTopProducts($sellerId, $limit = 10) {
        $products = $this->analyticsModel->getProductAnalytics($sellerId);
        return array_slice($products, 0, $limit);
    }
}
?>
