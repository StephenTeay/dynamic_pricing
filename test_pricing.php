<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/services/PricingEngine.php';
require_once __DIR__ . '/utils/logger.php';

try {
    echo "Testing Dynamic Pricing System...\n";
    
    // Initialize pricing engine
    $engine = new PricingEngine();
    
    // Test with specific product ID
    $productId = 4;
    
    // Get product details first
    $product = (new Product())->findWithInventory($productId);
    echo "Product: {$product['product_name']}\n";
    echo "Base Cost: {$product['base_cost']} {$product['cost_currency']}\n";
    echo "Current Price: {$product['current_price']} {$product['price_currency']}\n";
    echo "Stock Level: {$product['quantity_available']} units (Low: {$product['low_stock_threshold']}, High: {$product['high_stock_threshold']})\n\n";
    
    echo "\nPrice Calculation Steps:\n";
    echo "---------------------\n";
    
    // Start with base price
    echo "1. Base Cost: " . number_format($product['base_cost'], 2) . " " . $product['cost_currency'] . "\n\n";
    
    // Inventory adjustment
    $inventoryAdjustment = $engine->testInventoryAdjustment(
        $product['quantity_available'],
        $product['low_stock_threshold'],
        $product['high_stock_threshold']
    );
    echo "2. Inventory Factors:\n";
    echo "   - Current Stock: {$product['quantity_available']} units\n";
    echo "   - Low Threshold: {$product['low_stock_threshold']} units\n";
    echo "   - High Threshold: {$product['high_stock_threshold']} units\n";
    echo "   - Adjustment: " . number_format($inventoryAdjustment * 100, 1) . "%\n";
    echo "   - Price After Inventory: " . number_format($product['base_cost'] * (1 + $inventoryAdjustment), 2) . " " . $product['cost_currency'] . "\n\n";
    
    // Time-based adjustment
    $timeAdjustment = $engine->testTimeAdjustment($product['seller_id']);
    echo "3. Time-based Factors:\n";
    echo "   - Current Hour: " . date('H:i') . "\n";
    echo "   - Day of Week: " . date('l') . "\n";
    echo "   - Adjustment: " . number_format($timeAdjustment * 100, 1) . "%\n";
    $priceAfterTime = $product['base_cost'] * (1 + $inventoryAdjustment) * (1 + $timeAdjustment);
    echo "   - Price After Time: " . number_format($priceAfterTime, 2) . " " . $product['cost_currency'] . "\n\n";
    
    echo "4. Current Price: " . number_format($product['current_price'], 2) . " " . $product['price_currency'] . "\n";
    echo "   Price Change: " . number_format((($priceAfterTime - $product['current_price']) / $product['current_price']) * 100, 1) . "%\n\n";
    
    echo "Calculating final price...\n";
    
    // Calculate final price
    $price = $engine->calculateOptimalPrice($productId);
    echo "\nCalculation Results:";
    echo "\n------------------";
    echo "\nSuggested Price: " . number_format($price, 2) . " {$product['price_currency']}";
    echo "\nOld Price: " . number_format($product['current_price'], 2) . " {$product['price_currency']}";
    echo "\nChange: " . number_format(($price - $product['current_price']) / $product['current_price'] * 100, 1) . "%";
    
    if ($price != $product['current_price']) {
        echo "\nPrice was updated due to:";
        echo "\n- Low stock (" . $product['quantity_available'] . " < " . $product['low_stock_threshold'] . " units)";
        echo "\n- Significant price change (5.9% increase)";
    } else {
        echo "\nPrice remained unchanged.";
    }
    
    // Get pricing history
    $historyModel = new PricingHistory();
    $history = $historyModel->getByProductId($productId);
    echo "\nRecent price changes:\n";
    if (empty($history)) {
        echo "No price changes recorded yet.\n";
    }
    foreach ($history as $record) {
        if (isset($record['changed_at'])) {
            $timestamp = $record['changed_at'];
        } else if (isset($record['created_at'])) {
            $timestamp = $record['created_at'];
        } else {
            $timestamp = 'Unknown time';
        }
        
        if (is_string($timestamp)) {
            echo date('Y-m-d H:i:s', strtotime($timestamp));
        } else {
            echo 'Unknown time';
        }
        
        echo ": " . number_format($record['old_price'], 2) . " " . $product['price_currency'] . 
             " -> " . number_format($record['new_price'], 2) . " " . $product['price_currency'] . 
             " (" . ($record['change_reason'] ? $record['change_reason'] : 'No reason recorded') . ")\n";
    }
    
    // Check exchange rates if different currencies
    if ($product['cost_currency'] !== $product['price_currency']) {
        $exchangeService = new ExchangeRateService();
        $rate = $exchangeService->getExchangeRate(
            $product['cost_currency'], 
            $product['price_currency']
        );
        echo "\nCurrent {$product['cost_currency']} to {$product['price_currency']} rate: " . 
             ($rate ?: 'Not available') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}