<?php
// cron/generate_analytics.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/AnalyticsService.php';
require_once __DIR__ . '/../models/Order.php';

Logger::info('Starting analytics generation cron job');

try {
    $analyticsService = new AnalyticsService();
    $orderModel = new Order();
    
    // Get all sellers and generate analytics
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT DISTINCT seller_id FROM products");
    $stmt->execute();
    $sellers = $stmt->fetchAll();
    
    $count = 0;
    foreach ($sellers as $seller) {
        $analytics = $analyticsService->getSalesAnalytics($seller['seller_id'], 30);
        // Store analytics if needed
        $count++;
    }
    
    Logger::info("Analytics generation completed for $count sellers");
    
} catch (Exception $e) {
    Logger::error('Analytics generation failed: ' . $e->getMessage());
}
?>
