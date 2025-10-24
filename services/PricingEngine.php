<?php
// services/PricingEngine.php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../models/PricingRule.php';
require_once __DIR__ . '/../models/PricingHistory.php';
require_once __DIR__ . '/../services/ExchangeRateService.php';
require_once __DIR__ . '/../utils/logger.php';

class PricingEngine {
    // Price adjustment constants
    const MAX_PRICE_INCREASE = 0.20; // Maximum 20% increase
    const MAX_PRICE_DECREASE = 0.15; // Maximum 15% decrease
    const MIN_PROFIT_MARGIN = 0.10; // Minimum 10% profit margin
    
    private $db;
    private $productModel;
    private $inventoryModel = null;
    private $pricingRuleModel;
    private $pricingHistoryModel;
    private $exchangeRateService;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->productModel = new Product();
        $this->pricingRuleModel = new PricingRule();
        $this->pricingHistoryModel = new PricingHistory();
        $this->exchangeRateService = new ExchangeRateService();
    }
    
    private function getInventoryModel() {
        if ($this->inventoryModel === null) {
            $this->inventoryModel = new Inventory();
        }
        return $this->inventoryModel;
    }
    
    /**
     * Calculate optimal price based on all factors
     */
    public function calculateOptimalPrice($productId) {
        try {
            $product = $this->productModel->findWithInventory($productId);
            if (!$product) {
                throw new Exception("Product not found");
            }

            $basePrice = floatval($product['base_cost']);
            $currentPrice = floatval($product['current_price']);
            
            // Start with the base cost
            $newPrice = $basePrice;
            
            // Ensure minimum price is at least the base cost
            if ($currentPrice < $basePrice) {
                $currentPrice = $basePrice;
            }
        
            // 1. Apply currency conversion if needed
            if ($product['cost_currency'] !== $product['price_currency']) {
                try {
                    $newPrice = $this->exchangeRateService->convert(
                        $newPrice,
                        $product['cost_currency'],
                        $product['price_currency']
                    );
                } catch (Exception $e) {
                    Logger::error("Currency conversion failed: " . $e->getMessage());
                }
            }

            // 2. Apply inventory-based pricing
            $inventoryAdjustment = $this->calculateInventoryAdjustment(
                $product['quantity_available'],
                $product['low_stock_threshold'],
                $product['high_stock_threshold']
            );
            $newPrice *= (1 + $inventoryAdjustment);

            // 3. Apply time-based adjustments
            $timeAdjustment = $this->calculateTimeBasedAdjustment($product['seller_id']);
            $newPrice *= (1 + $timeAdjustment);

            // 4. Apply demand-based pricing
            $demandAdjustment = $this->calculateDemandAdjustment($productId);
            $newPrice *= (1 + $demandAdjustment);

            // 5. Apply seller's custom pricing rules
            $rules = $this->pricingRuleModel->getActiveRules($product['seller_id'], $productId);
            foreach ($rules as $rule) {
                $newPrice = $this->applyPricingRule($newPrice, $rule);
            }

            // 6. Ensure price stays within allowed limits
            $newPrice = $this->enforcePriceLimits($newPrice, $basePrice);

            // Round to 2 decimal places
            $newPrice = round($newPrice, 2);
            
            Logger::info("\nDetailed price calculation:");
            Logger::info("Base cost: {$basePrice}");
            Logger::info("Current price: {$currentPrice}");
            Logger::info("New price (before adjust): {$newPrice}");
            Logger::info("Stock level: " . $product['quantity_available'] . " of " . $product['low_stock_threshold']);

            // Calculate price change percentage
            $priceChange = ($newPrice - $currentPrice) / $currentPrice;
            $percentChange = round($priceChange * 100, 2);
            
            // Determine price update criteria
            $isLowStock = $product['quantity_available'] < $product['low_stock_threshold'];
            $isHighStock = $product['quantity_available'] >= $product['high_stock_threshold'];
            $isSignificantChange = abs($percentChange) > 1.0; // Change to use percentChange and 1% threshold
            $isPriceIncrease = $newPrice > $currentPrice;
            
            Logger::info("\nUpdate criteria:");
            Logger::info("- Low stock? " . ($isLowStock ? "Yes" : "No"));
            Logger::info("- Price increasing? " . ($isPriceIncrease ? "Yes" : "No"));
            Logger::info("- Significant change? " . ($isSignificantChange ? "Yes" : "No"));
            Logger::info("- Price change: {$percentChange}%");
            Logger::info("- Current: {$currentPrice}, New: {$newPrice}");
            
            // Update if:
            // 1. Low stock condition OR
            // 2. High stock condition OR
            // 3. Change is more than 1%
            $shouldUpdate = $isLowStock || $isHighStock || $isSignificantChange;
            
            Logger::info("Should update? " . ($shouldUpdate ? "Yes" : "No"));
            if ($shouldUpdate) {
                Logger::info("Update reason: " . 
                    ($isLowStock && $isPriceIncrease ? "Low stock and price increase" : 
                     "Significant price change ({$percentChange}%)")
                );
            }
            
            Logger::info("\nPrice calculation details:");
            Logger::info("- Current price: {$currentPrice}");
            Logger::info("- New price: {$newPrice}");
            Logger::info("- Change: {$percentChange}%");
            Logger::info("- Stock level: {$product['quantity_available']} units");
            Logger::info("- Low threshold: {$product['low_stock_threshold']} units");
            Logger::info("- Stock status: " . ($isLowStock ? "LOW" : "Normal") . 
                        ($isHighStock ? " (High stock)" : ""));
            
            // Log update conditions
            $conditions = [];
            if ($isLowStock) $conditions[] = "Low stock";
            if ($isSignificantChange) $conditions[] = "Significant price change ({$percentChange}%)";
            Logger::info("- Update conditions: " . ($conditions ? implode(", ", $conditions) : "None"));
            
            // Update price if needed
            if ($shouldUpdate) {
                try {
                    $updateReason = $isLowStock ? "LOW STOCK: Forcing price increase" : "SIGNIFICANT CHANGE: Regular price adjustment";
                    Logger::info("Updating price - " . $updateReason);
                    
                    $this->updateProductPrice($productId, $currentPrice, $newPrice);
                    Logger::info("Price successfully updated from {$currentPrice} to {$newPrice} ({$percentChange}% change)");
                    return $newPrice;
                } catch (Exception $e) {
                    Logger::error("Failed to update price: " . $e->getMessage());
                    return $currentPrice;
                }
            } else {
                Logger::info("NO SIGNIFICANT CHANGE: Keeping current price");
                return $currentPrice;
            }

        } catch (Exception $e) {
            Logger::error("Price calculation failed for product {$productId}: " . $e->getMessage());
            return $currentPrice; // Return current price if calculation fails
        }
    }

    /**
     * Calculate inventory-based price adjustment
     */
    private function calculateInventoryAdjustment($currentStock, $lowThreshold, $highThreshold) {
        // No adjustment if thresholds are not set
        if (!$lowThreshold || !$highThreshold) {
            return 0;
        }

        // Calculate stock ratio
        if ($currentStock <= $lowThreshold) {
            // Low stock: increase price up to MAX_PRICE_INCREASE
            // More aggressive increase as stock gets lower
            $ratio = 1 - (pow($currentStock / $lowThreshold, 2));
            return $ratio * self::MAX_PRICE_INCREASE;
        } 
        
        if ($currentStock >= $highThreshold) {
            // High stock: decrease price up to MAX_PRICE_DECREASE
            $ratio = ($currentStock - $highThreshold) / $highThreshold;
            return -min($ratio * self::MAX_PRICE_DECREASE, self::MAX_PRICE_DECREASE);
        }
        
        // Gradual adjustment between thresholds
        $midPoint = ($highThreshold + $lowThreshold) / 2;
        if ($currentStock < $midPoint) {
            // Slight increase for below-optimal stock
            $ratio = ($midPoint - $currentStock) / ($midPoint - $lowThreshold);
            return $ratio * (self::MAX_PRICE_INCREASE / 2);
        } else {
            // Slight decrease for above-optimal stock
            $ratio = ($currentStock - $midPoint) / ($highThreshold - $midPoint);
            return -($ratio * (self::MAX_PRICE_DECREASE / 2));
        }
    }

    /**
     * Calculate time-based price adjustments
     */
    private function calculateTimeBasedAdjustment($sellerId) {
        $adjustment = 0;
        $hour = (int)date('H');
        $dayOfWeek = (int)date('N');
        
        // Peak hours (e.g., 12-2pm and 6-8pm): +5%
        if (($hour >= 12 && $hour <= 14) || ($hour >= 18 && $hour <= 20)) {
            $adjustment += 0.05;
        }
        
        // Weekend pricing: +10%
        if ($dayOfWeek >= 6) {
            $adjustment += 0.10;
        }
        
        return $adjustment;
    }
    
    // Test methods to expose private calculations
    public function testInventoryAdjustment($stock, $low, $high) {
        return $this->calculateInventoryAdjustment($stock, $low, $high);
    }
    
    public function testTimeAdjustment($sellerId) {
        return $this->calculateTimeBasedAdjustment($sellerId);
    }

    /**
     * Calculate demand-based price adjustments
     */
    private function calculateDemandAdjustment($productId) {
        try {
            // Get orders in last 24 hours
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as order_count
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.order_id
                WHERE oi.product_id = :product_id
                AND o.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ");
            $stmt->execute([':product_id' => $productId]);
            $result = $stmt->fetch();
            
            // Adjust price based on recent order volume
            $orderCount = $result['order_count'];
            if ($orderCount > 10) {
                return 0.15; // High demand: +15%
            } elseif ($orderCount > 5) {
                return 0.08; // Medium demand: +8%
            } elseif ($orderCount < 1) {
                return -0.05; // Low demand: -5%
            }
            
            return 0;
            
        } catch (Exception $e) {
            Logger::error("Demand calculation failed: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Apply pricing rule
     */
    private function applyPricingRule($price, $rule) {
        if (!$rule['is_active']) {
            return $price;
        }

        switch ($rule['rule_type']) {
            case 'fixed':
                return $rule['min_value']; // Use min_value as fixed price

            case 'percentage':
                return $price * (1 + ($rule['percentage_change'] / 100));

            case 'range':
                // Keep price within specified range
                return max(min($price, $rule['max_value']), $rule['min_value']);
        }

        return $price;
    }

    /**
     * Enforce minimum and maximum price limits
     */
    private function enforcePriceLimits($price, $baseCost) {
        // Ensure minimum profit margin
        $minPrice = $baseCost * (1 + self::MIN_PROFIT_MARGIN);
        
        // Ensure maximum markup
        $maxPrice = $baseCost * (1 + self::MAX_PRICE_INCREASE);
        
        Logger::info("Price limits:");
        Logger::info("- Original price: {$price}");
        Logger::info("- Min price (10% margin): {$minPrice}");
        Logger::info("- Max price (20% increase): {$maxPrice}");
        
        $finalPrice = max(min($price, $maxPrice), $minPrice);
        Logger::info("- Final price after limits: {$finalPrice}");
        
        return $finalPrice;
    }
        
        /**
     * Update product price and log the change
     */
    private function updateProductPrice($productId, $oldPrice, $newPrice) {
        try {
            // Validate inputs
            if (!is_numeric($productId) || $productId <= 0) {
                throw new \InvalidArgumentException("Invalid product ID: $productId");
            }
            if (!is_numeric($oldPrice) || $oldPrice < 0) {
                throw new \InvalidArgumentException("Invalid old price: $oldPrice");
            }
            if (!is_numeric($newPrice) || $newPrice < 0) {
                throw new \InvalidArgumentException("Invalid new price: $newPrice");
            }
            
            Logger::info("Starting price update transaction for product {$productId}");
            Logger::info("Current price: {$oldPrice}, New price: {$newPrice}");
            
            $this->db->beginTransaction();
            
            // 1. Update product price
            $stmt = $this->db->prepare("
                UPDATE products 
                SET current_price = :new_price,
                    last_price_update = NOW(),
                    updated_at = NOW()
                WHERE product_id = :product_id
            ");
            
            try {
                $result = $stmt->execute([
                    ':new_price' => $newPrice,
                    ':product_id' => $productId
                ]);
                
                if ($stmt->rowCount() === 0) {
                    throw new \Exception("No product found with ID: $productId");
                }
            } catch (\PDOException $e) {
                error_log("Failed to update product price for product $productId: " . $e->getMessage());
                throw new \Exception("Failed to update product price: " . $e->getMessage());
            }

            // 2. Log price change history
            $changePercent = (($newPrice - $oldPrice) / $oldPrice) * 100;
            
            // Get inventory status for change reason
            $product = $this->productModel->findWithInventory($productId);
            $isLowStock = $product['quantity_available'] <= $product['low_stock_threshold'];
            $isHighStock = $product['quantity_available'] >= $product['high_stock_threshold'];
            
            // Get specific reason for price change
            $isLowStock = $product['quantity_available'] <= $product['low_stock_threshold'];
            $isHighStock = $product['quantity_available'] >= $product['high_stock_threshold'];

            // Get reason based on stock level and price change
            $percentChange = (($newPrice - $oldPrice) / $oldPrice) * 100;
            
            if ($isLowStock) {
                $reason = sprintf(
                    "Low stock adjustment (Stock: %d of %d units) - Price %s by %.1f%%",
                    $product['quantity_available'],
                    $product['low_stock_threshold'],
                    $newPrice > $oldPrice ? "increased" : "decreased",
                    abs($percentChange)
                );
            } elseif ($isHighStock) {
                $reason = sprintf(
                    "High stock adjustment (Stock: %d exceeds %d units) - Price %s by %.1f%%",
                    $product['quantity_available'],
                    $product['high_stock_threshold'],
                    $newPrice > $oldPrice ? "increased" : "decreased",
                    abs($percentChange)
                );
            } else {
                $reason = sprintf(
                    "Normal stock level adjustment (%d units) - Price %s by %.1f%%",
                    $product['quantity_available'],
                    $newPrice > $oldPrice ? "increased" : "decreased",
                    abs($percentChange)
                );
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO pricing_history (
                    product_id,
                    old_price,
                    new_price,
                    price_change_percent,
                    change_reason
                ) VALUES (
                    :product_id,
                    :old_price,
                    :new_price,
                    :change_percent,
                    :reason
                )
            ");

            try {
                $stmt->execute([
                    ':product_id' => $productId,
                    ':old_price' => $oldPrice,
                    ':new_price' => $newPrice,
                    ':change_percent' => round($changePercent, 2),
                    ':reason' => $reason  // Use the reason we determined earlier
                ]);
            } catch (\PDOException $e) {
                error_log("Failed to log price history for product $productId: " . $e->getMessage());
                throw new \Exception("Failed to log price history: " . $e->getMessage());
            }

            // 3. Create notification for significant price changes (>10%)
            try {
                if (abs($changePercent) > 10) {
                    $this->createPriceChangeNotification($productId, $oldPrice, $newPrice, $changePercent);
                }
            } catch (\Exception $e) {
                error_log("Failed to create price change notification: " . $e->getMessage());
                // Don't throw here, as notification failure shouldn't rollback the price update
            }

            $this->db->commit();
            
            Logger::info("Successfully updated price for product $productId: $oldPrice -> $newPrice");
            return true;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            $error = "Database error while updating price for product {$productId}: " . $e->getMessage();
            Logger::error($error);
            throw new \Exception($error);
        } catch (\Exception $e) {
            $this->db->rollBack();
            $error = "Failed to update price for product {$productId}: " . $e->getMessage();
            Logger::error($error);
            throw new \Exception($error);
        }
    }

    /**
     * Determine the reason for price change
     */
    private function determinePriceChangeReason($oldPrice, $newPrice) {
        $percentChange = (($newPrice - $oldPrice) / $oldPrice) * 100;
        
        if ($percentChange > 0) {
            return $percentChange > 20 
                ? 'Significant price increase due to high demand or low stock'
                : 'Regular price adjustment (increase)';
        } else {
            return $percentChange < -20
                ? 'Significant price decrease due to low demand or high stock'
                : 'Regular price adjustment (decrease)';
        }
    }

    /**
     * Create notification for significant price changes
     */
    private function createPriceChangeNotification($productId, $oldPrice, $newPrice, $percentChange) {
        try {
            // Get product and seller details
            $product = $this->productModel->find($productId);
            if (!$product) {
                throw new \Exception("Product not found: $productId");
            }
            
            $changeDirection = $percentChange > 0 ? 'increased' : 'decreased';
            $title = "Significant Price {$changeDirection} - {$product['product_name']}";
            $message = sprintf(
                "Price for product '%s' has %s by %.1f%% (from %.2f to %.2f). This change was triggered by our dynamic pricing system.",
                $product['product_name'],
                $changeDirection,
                abs($percentChange),
                $oldPrice,
                $newPrice
            );

            $stmt = $this->db->prepare("
                INSERT INTO notifications (
                    user_id,
                    type,
                    title,
                    message,
                    is_read,
                    is_sent,
                    created_at
                ) VALUES (
                    :user_id,
                    'price_change',
                    :title,
                    :message,
                    0,
                    0,
                    NOW()
                )
            ");

            try {
                $stmt->execute([
                    ':user_id' => $product['seller_id'],
                    ':title' => $title,
                    ':message' => $message
                ]);
            } catch (Exception $e) {
                Logger::error("Failed to create price change notification: " . $e->getMessage());
                throw $e;
            }
        } catch (Exception $e) {
            Logger::error("Failed to create price change notification: " . $e->getMessage());
        }
    }

    /**
     * Validate if a price is within acceptable range
     */
    public function validatePrice($productId, $price) {
        $product = $this->productModel->find($productId);
        
        if (!$product) {
            return false;
        }
        
        $basePrice = $product['base_cost'];
        $minPrice = $basePrice * (1 + MIN_PROFIT_MARGIN);
        $maxPrice = $basePrice * (1 + MAX_PRICE_INCREASE);
        
        return $price >= $minPrice && $price <= $maxPrice;
    }
}

?>
