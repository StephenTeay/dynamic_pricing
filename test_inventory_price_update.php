<?php
// test_inventory_price_update.php

require_once __DIR__ . '/models/Inventory.php';
require_once __DIR__ . '/utils/logger.php';

// Test with the Italian Chairs product (ID: 4)
$productId = 4;
$newQuantity = 1; // Set to critically low stock level

$inventory = new Inventory();

try {
    echo "Current inventory status:\n";
    $before = $inventory->getByProductId($productId);
    echo "Quantity before: " . $before['quantity_available'] . "\n";
    
    echo "\nUpdating stock...\n";
    $result = $inventory->updateStock($productId, $newQuantity);
    
    if ($result) {
        $after = $inventory->getByProductId($productId);
        echo "Stock update successful!\n";
        echo "Quantity after: " . $after['quantity_available'] . "\n";
        echo "\nCheck the logs for price update details.\n";
    } else {
        echo "Failed to update stock.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}