<?php
// cron/check_inventory_and_update_prices.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/PricingEngine.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../utils/logger.php';

Logger::info('Starting inventory check and price update job');

try {
    $db = (new Database())->getConnection();
    $pricingEngine = new PricingEngine();

    // Get all products with low stock
    $stmt = $db->prepare("
        SELECT 
            p.*,
            i.quantity_available,
            i.quantity_reserved,
            i.low_stock_threshold,
            i.high_stock_threshold
        FROM products p
        INNER JOIN inventory i ON p.product_id = i.product_id
        WHERE i.quantity_available <= i.low_stock_threshold
        AND p.is_active = 1
        AND p.last_price_update <= DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updatedCount = 0;
    $unchangedCount = 0;
    $failedCount = 0;
    
    foreach ($products as $product) {
        try {
            Logger::info("Checking product {$product['product_id']} ({$product['product_name']})");
            Logger::info("Current stock: {$product['quantity_available']} (Threshold: {$product['low_stock_threshold']})");
            
            // Calculate new price
            $newPrice = $pricingEngine->calculateOptimalPrice($product['product_id']);
            
            if ($newPrice != $product['current_price']) {
                $updatedCount++;
                Logger::info(sprintf(
                    "Updated price for product %d (%s): %s%.2f -> %s%.2f",
                    $product['product_id'],
                    $product['product_name'],
                    $product['price_currency'],
                    $product['current_price'],
                    $product['price_currency'],
                    $newPrice
                ));
            } else {
                $unchangedCount++;
            }
        } catch (Exception $e) {
            $failedCount++;
            Logger::error(sprintf(
                "Failed to update price for product %d (%s): %s",
                $product['product_id'],
                $product['product_name'],
                $e->getMessage()
            ));
        }
    }
    
    Logger::info(sprintf(
        "Inventory check completed. Products processed: %d (Updated: %d, Unchanged: %d, Failed: %d)",
        count($products),
        $updatedCount,
        $unchangedCount,
        $failedCount
    ));

} catch (Exception $e) {
    Logger::error("Failed to run inventory check job: " . $e->getMessage());
    exit(1);
}