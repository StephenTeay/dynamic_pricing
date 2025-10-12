<?php
// services/PricingEngine.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../models/PricingRule.php';
require_once __DIR__ . '/../config/config.php';

class PricingEngine {
    private $productModel;
    private $inventoryModel;
    private $pricingRuleModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->inventoryModel = new Inventory();
        $this->pricingRuleModel = new PricingRule();
    }
    
    /**
     * Calculate dynamic price based on inventory
     */
    public function calculateDynamicPrice($productId) {
        $product = $this->productModel->find($productId);
        $inventory = $this->inventoryModel->getByProductId($productId);
        
        if (!$product || !$inventory) {
            return $product['current_price'] ?? 0;
        }
        
        $basePrice = $product['base_cost'];
        $currentPrice = $product['current_price'];
        $quantity = $inventory['quantity_available'];
        $lowThreshold = $inventory['low_stock_threshold'];
        $highThreshold = $inventory['high_stock_threshold'];
        
        $newPrice = $currentPrice;
        
        // Low stock - increase price
        if ($quantity <= $lowThreshold && $quantity > 0) {
            $priceIncrease = $basePrice * MIN_PROFIT_MARGIN * 1.5;
            $newPrice = min($basePrice + $priceIncrease, $basePrice * (1 + MAX_PRICE_INCREASE));
        }
        // High stock - decrease price
        elseif ($quantity >= $highThreshold) {
            $priceDecrease = $basePrice * MIN_PROFIT_MARGIN * 0.5;
            $newPrice = max($basePrice + $priceDecrease, $basePrice * (1 - MAX_PRICE_DECREASE));
        }
        // Optimal stock - standard pricing
        else {
            $newPrice = $basePrice * (1 + MIN_PROFIT_MARGIN);
        }
        
        return round($newPrice, 2);
    }
    
    /**
     * Apply pricing rule
     */
    public function applyPricingRule($productId, $ruleId) {
        $product = $this->productModel->find($productId);
        $rule = $this->pricingRuleModel->find($ruleId);
        
        if (!$product || !$rule) {
            return false;
        }
        
        $basePrice = $product['base_cost'];
        $newPrice = $basePrice;
        
        switch ($rule['rule_type']) {
            case PRICING_RULE_FIXED:
                $newPrice = $rule['fixed_price'];
                break;
                
            case PRICING_RULE_PERCENTAGE:
                $newPrice = $basePrice * (1 + ($rule['percentage'] / 100));
                break;
                
            case PRICING_RULE_DYNAMIC:
                $newPrice = $this->calculateDynamicPrice($productId);
                break;
        }
        
        return $this->productModel->updatePrice($productId, round($newPrice, 2), $rule['rule_name']);
    }
    
    /**
     * Validate pricing
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
