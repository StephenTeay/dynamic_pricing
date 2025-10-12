<?php
// controllers/PricingController.php

require_once __DIR__ . '/../models/PricingRule.php';
require_once __DIR__ . '/../models/PricingHistory.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../core/Session.php';

class PricingController {
    private $pricingRuleModel;
    private $pricingHistoryModel;
    private $productModel;
    
    public function __construct() {
        $this->pricingRuleModel = new PricingRule();
        $this->pricingHistoryModel = new PricingHistory();
        $this->productModel = new Product();
    }
    
    /**
     * Get pricing rules for product
     */
    public function getRules($productId) {
        return $this->pricingRuleModel->getByProductId($productId);
    }
    
    /**
     * Create pricing rule
     */
    public function createRule($data) {
        $product = $this->productModel->find($data['product_id']);
        
        if (!$product || $product['seller_id'] != Session::getUserId()) {
            return false;
        }
        
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->pricingRuleModel->create($data);
    }
    
    /**
     * Update pricing rule
     */
    public function updateRule($ruleId, $data) {
        $rule = $this->pricingRuleModel->find($ruleId);
        
        if (!$rule) {
            return false;
        }
        
        $product = $this->productModel->find($rule['product_id']);
        
        if ($product['seller_id'] != Session::getUserId()) {
            return false;
        }
        
        return $this->pricingRuleModel->update($ruleId, $data);
    }
    
    /**
     * Delete pricing rule
     */
    public function deleteRule($ruleId) {
        $rule = $this->pricingRuleModel->find($ruleId);
        
        if (!$rule) {
            return false;
        }
        
        $product = $this->productModel->find($rule['product_id']);
        
        if ($product['seller_id'] != Session::getUserId()) {
            return false;
        }
        
        return $this->pricingRuleModel->delete($ruleId);
    }
    
    /**
     * Update product price
     */
    public function updatePrice($productId, $newPrice, $reason = '') {
        $product = $this->productModel->find($productId);
        
        if (!$product || $product['seller_id'] != Session::getUserId()) {
            return false;
        }
        
        return $this->productModel->updatePrice($productId, $newPrice, $reason);
    }
    
    /**
     * Get pricing history
     */
    public function getPriceHistory($productId) {
        return $this->pricingHistoryModel->getByProductId($productId);
    }
}
?>
