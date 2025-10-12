<?php
// models/PricingRule.php

require_once __DIR__ . '/../core/Model.php';

class PricingRule extends Model {
    protected $table = 'pricing_rules';
    protected $primaryKey = 'rule_id';
    
    public function getByProductId($productId) {
        return $this->findAll(['product_id' => $productId], 'created_at DESC');
    }
    
    public function createRule($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
?>
