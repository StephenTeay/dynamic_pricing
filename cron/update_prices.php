<?php
// cron/update_prices.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/PricingEngine.php';
require_once __DIR__ . '/../services/ExchangeRateService.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/PricingRule.php';
require_once __DIR__ . '/../utils/logger.php';

Logger::info('Starting automated price update job');

try {
    $db = (new Database())->getConnection();
    $pricingEngine = new PricingEngine();
    $exchangeRateService = new ExchangeRateService();

    // 1. First update exchange rates
    Logger::info('Updating exchange rates...');
    $exchangeRateService->updateAllRates();

    // 2. Get products that need price updates
    $stmt = $db->prepare("
        SELECT 
            p.*,
            i.quantity_available,
            i.quantity_reserved,
            i.low_stock_threshold,
            i.high_stock_threshold
        FROM products p
        INNER JOIN inventory i ON p.product_id = i.product_id
        WHERE p.is_active = 1
        AND (
            -- Products not updated in last hour
            p.last_price_update IS NULL 
            OR p.last_price_update < DATE_SUB(NOW(), INTERVAL 1 HOUR)
            OR
            -- Products with low stock
            i.quantity_available <= i.low_stock_threshold
            OR
            -- Products with high stock
            i.quantity_available >= i.high_stock_threshold
            OR
            -- Products with active pricing rules
            EXISTS (
                SELECT 1 FROM pricing_rules pr 
                WHERE (pr.product_id = p.product_id OR pr.product_id IS NULL)
                AND pr.seller_id = p.seller_id
                AND pr.is_active = 1
            )
        )
        ORDER BY p.last_price_update ASC
    ");

    $stmt->execute();
    $products = $stmt->fetchAll();

    $updatedCount = 0;
    $failedCount = 0;
    $unchangedCount = 0;

    foreach ($products as $product) {
        try {
            $oldPrice = $product['current_price'];
            $newPrice = $pricingEngine->calculateOptimalPrice($product['product_id']);

            // Only log if price actually changed
            if (abs(($newPrice - $oldPrice) / $oldPrice) > 0.01) { // 1% threshold
                $updatedCount++;
                Logger::info(sprintf(
                    "Updated price for product %d (%s): %s%.2f -> %s%.2f",
                    $product['product_id'],
                    $product['product_name'],
                    $product['price_currency'],
                    $oldPrice,
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
        "Price update completed. Updated: %d products, Unchanged: %d products, Failed: %d products",
        $updatedCount,
        $unchangedCount,
        $failedCount
    ));

} catch (Exception $e) {
    Logger::error("Price update job failed: " . $e->getMessage());
    // Notify admin about the failure
    mail(
        ADMIN_EMAIL,
        "Price Update Job Failed",
        "The automated price update job failed with error: " . $e->getMessage()
    );
}
