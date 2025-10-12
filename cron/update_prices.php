<?php
// cron/update_prices.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/PricingEngine.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/PricingRule.php';

Logger::info('Starting price update cron job');

try {
    $pricingEngine = new PricingEngine();
    $productModel = new Product();
    $pricingRuleModel = new PricingRule();
    
    // Get all active products with dynamic pricing rules
    $query = "SELECT DISTINCT p.product_id 
              FROM products p
              INNER JOIN pricing_rules pr ON p.product_id = pr.product_id
              WHERE p.is_active = 1 AND pr.rule_type = 'dynamic'";
    
    $db = (new Database())->getConnection();
    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll();
    
    $updated = 0;
    foreach ($products as $product) {
        $newPrice = $pricingEngine->calculateDynamicPrice($product['product_id']);
        $productModel->updatePrice($product['product_id'], $newPrice, 'Automatic dynamic pricing');
        $updated++;
    }
    
    Logger::info("Price update completed. Updated $updated products");
    
} catch (Exception $e) {
    Logger::error('Price update cron job failed: ' . $e->getMessage());
}
?>
