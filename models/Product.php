<?php
// models/Product.php

require_once __DIR__ . '/../core/Model.php';

class Product extends Model {
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $hasOne = ['Inventory'];
    
    /**
     * Get products by category
     */
    public function getProductsByCategory($category, $limit = 20) {
        $query = "SELECT p.*, i.quantity_available, i.low_stock_threshold 
                  FROM {$this->table} p
                  LEFT JOIN inventory i ON p.product_id = i.product_id
                  WHERE p.category = :category AND p.is_active = 1
                  ORDER BY p.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':category', $category);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Search products
     */
    public function searchProducts($search, $category = null) {
        $query = "SELECT p.*, i.quantity_available, i.low_stock_threshold 
                  FROM {$this->table} p
                  LEFT JOIN inventory i ON p.product_id = i.product_id
                  WHERE p.is_active = 1 
                  AND (p.product_name LIKE :search 
                       OR p.product_description LIKE :search 
                       OR p.sku LIKE :search)";
        
        if ($category) {
            $query .= " AND p.category = :category";
        }
        
        $query .= " ORDER BY p.created_at DESC LIMIT 50";
        
        $stmt = $this->db->prepare($query);
        $searchTerm = "%{$search}%";
        $stmt->bindValue(':search', $searchTerm);
        
        if ($category) {
            $stmt->bindValue(':category', $category);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get product detail with inventory and seller info
     */
    public function getProductDetail($productId) {
        $query = "SELECT p.*, 
                         i.quantity_available, i.quantity_reserved,
                         i.low_stock_threshold, i.high_stock_threshold,
                         sp.business_name, sp.business_email, sp.business_phone
                  FROM {$this->table} p
                  LEFT JOIN inventory i ON p.product_id = i.product_id
                  LEFT JOIN seller_profiles sp ON p.seller_id = sp.seller_id
                  WHERE p.product_id = :product_id AND p.is_active = 1
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':product_id', $productId);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get products with inventory for seller
     */
    public function getProductsWithInventory($sellerId) {
        $query = "SELECT p.*, 
                         i.quantity_available, i.quantity_reserved,
                         i.low_stock_threshold, i.high_stock_threshold
                  FROM {$this->table} p
                  LEFT JOIN inventory i ON p.product_id = i.product_id
                  WHERE p.seller_id = :seller_id AND p.is_active = 1
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':seller_id', $sellerId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get low stock products for seller
     */
    public function getLowStockProducts($sellerId) {
        $query = "SELECT p.*, i.quantity_available, i.low_stock_threshold
                  FROM {$this->table} p
                  INNER JOIN inventory i ON p.product_id = i.product_id
                  WHERE p.seller_id = :seller_id 
                  AND p.is_active = 1
                  AND i.quantity_available <= i.low_stock_threshold
                  ORDER BY i.quantity_available ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':seller_id', $sellerId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Update product price
     */
    public function updatePrice($productId, $newPrice, $reason = '') {
        // Get current price
        $product = $this->find($productId);
        
        if (!$product) {
            return false;
        }
        
        $oldPrice = $product['current_price'];
        
        // Update price
        $this->update($productId, [
            'current_price' => $newPrice,
            'last_price_update' => date('Y-m-d H:i:s')
        ]);
        
        // Log price change
        $priceChange = (($newPrice - $oldPrice) / $oldPrice) * 100;
        
        $query = "INSERT INTO pricing_history 
                  (product_id, old_price, new_price, price_change_percent, change_reason, changed_at)
                  VALUES (:product_id, :old_price, :new_price, :change_percent, :reason, NOW())";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':product_id' => $productId,
            ':old_price' => $oldPrice,
            ':new_price' => $newPrice,
            ':change_percent' => $priceChange,
            ':reason' => $reason
        ]);
        
        return true;
    }
    
    /**
     * Get trending products
     */
    public function getTrendingProducts($limit = 10) {
        $query = "SELECT p.*, 
                         COUNT(oi.order_item_id) as order_count,
                         i.quantity_available
                  FROM {$this->table} p
                  LEFT JOIN order_items oi ON p.product_id = oi.product_id
                  LEFT JOIN inventory i ON p.product_id = i.product_id
                  WHERE p.is_active = 1
                  AND oi.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  GROUP BY p.product_id
                  ORDER BY order_count DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get featured products
     */
    public function getFeaturedProducts($limit = 6) {
        $query = "SELECT p.*, i.quantity_available
                  FROM {$this->table} p
                  LEFT JOIN inventory i ON p.product_id = i.product_id
                  WHERE p.is_active = 1
                  AND i.quantity_available > 0
                  ORDER BY RAND()
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get products by price range
     */
    public function getProductsByPriceRange($minPrice, $maxPrice, $category = null) {
        $query = "SELECT p.*, i.quantity_available
                  FROM {$this->table} p
                  LEFT JOIN inventory i ON p.product_id = i.product_id
                  WHERE p.is_active = 1
                  AND p.current_price BETWEEN :min_price AND :max_price";
        
        if ($category) {
            $query .= " AND p.category = :category";
        }
        
        $query .= " ORDER BY p.current_price ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':min_price', $minPrice);
        $stmt->bindValue(':max_price', $maxPrice);
        
        if ($category) {
            $stmt->bindValue(':category', $category);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get related products
     */
    public function getRelatedProducts($productId, $limit = 4) {
        $product = $this->find($productId);
        
        if (!$product) {
            return [];
        }
        
        $query = "SELECT p.*, i.quantity_available
                  FROM {$this->table} p
                  LEFT JOIN inventory i ON p.product_id = i.product_id
                  WHERE p.category = :category 
                  AND p.product_id != :product_id
                  AND p.is_active = 1
                  ORDER BY RAND()
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':category', $product['category']);
        $stmt->bindValue(':product_id', $productId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}