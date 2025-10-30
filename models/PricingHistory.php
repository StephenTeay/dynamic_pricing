<?php
// models/PricingHistory.php

require_once __DIR__ . '/../core/Model.php';

class PricingHistory extends Model {
    protected $table = 'pricing_history';
    protected $primaryKey = 'history_id';
    
    public function getByProductId($productId) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE product_id = :product_id
            ORDER BY pricing_history_id DESC
        ");
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
