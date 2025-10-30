<?php
// models/Analytics.php

require_once __DIR__ . '/../core/Model.php';

class Analytics extends Model {
    protected $table = 'analytics';
    protected $primaryKey = 'analytics_id';
    
    public function getSalesAnalytics($sellerId, $days = 30) {
        $query = "SELECT DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue
                  FROM orders o
                  INNER JOIN order_items oi ON o.order_id = oi.order_id
                  INNER JOIN products p ON oi.product_id = p.product_id
                  WHERE p.seller_id = :seller_id
                  AND created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                  GROUP BY DATE(created_at)
                  ORDER BY date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':seller_id' => $sellerId, ':days' => $days]);
        return $stmt->fetchAll();
    }
    
    public function getProductAnalytics($sellerId) {
        $query = "SELECT p.product_id, p.product_name, COUNT(oi.order_item_id) as sales,
                         SUM(oi.subtotal) as revenue, AVG(p.current_price) as avg_price
                  FROM products p
                  LEFT JOIN order_items oi ON p.product_id = oi.product_id
                  WHERE p.seller_id = :seller_id
                  GROUP BY p.product_id
                  ORDER BY sales DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':seller_id' => $sellerId]);
        return $stmt->fetchAll();
    }
}
?>
