<?php
// models/PricingHistory.php

require_once __DIR__ . '/../core/Model.php';

class PricingHistory extends Model {
    protected $table = 'pricing_history';
    protected $primaryKey = 'history_id';
    
    public function getByProductId($productId) {
        return $this->findAll(['product_id' => $productId], 'changed_at DESC');
    }
}
?>
