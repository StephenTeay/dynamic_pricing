<?php
// models/PricingRule.php

require_once __DIR__ . '/../core/Model.php';

class PricingRule extends Model {
    protected $table = 'pricing_rules';
    protected $primaryKey = 'rule_id';
    
    public function getByProductId($productId) {
        return $this->findAll(['product_id' => $productId], 'created_at DESC');
    }
    
    public function getActiveRules($sellerId, $productId = null) {
        $query = "SELECT * FROM {$this->table} 
                 WHERE seller_id = ? 
                 AND is_active = 1 
                 AND (product_id IS NULL OR product_id = ?)
                 AND (start_date IS NULL OR start_date <= NOW())
                 AND (end_date IS NULL OR end_date >= NOW())
                 ORDER BY priority DESC, updated_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sellerId, $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function createRule($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
?>
