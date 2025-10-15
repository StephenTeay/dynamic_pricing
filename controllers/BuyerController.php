<?php
// controllers/BuyerController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../core/Session.php';

class BuyerController {
    private $productModel;
    private $orderModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->orderModel = new Order();
    }
    
    /**
     * Display shop with products
     */
    public function shop() {
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        $page = (int)($_GET['page'] ?? 1);
        $limit = PRODUCTS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        if ($search) {
            $products = $this->productModel->searchProducts($search, $category);
        } elseif ($category) {
            $products = $this->productModel->getProductsByCategory($category, $limit);
        } else {
            $products = $this->productModel->findAll(
                ['is_active' => 1],
                'created_at DESC',
                $limit,
                $offset
            );
        }
        
    include __DIR__ . '/../views/buyer/shop.php';
    }
    
    /**
     * Display product details
     */
    public function productDetail($productId) {
        $product = $this->productModel->getProductDetail($productId);
        
        if (!$product) {
            redirect('/buyer/shop');
            exit;
        }
        
        $relatedProducts = $this->productModel->getRelatedProducts($productId, 4);
        
        include __DIR__ . '/../views/buyer/product.php';
    }
    
    /**
     * Get featured products
     */
    public function getFeaturedProducts() {
        return $this->productModel->getFeaturedProducts(6);
    }
    
    /**
     * Get trending products
     */
    public function getTrendingProducts() {
        return $this->productModel->getTrendingProducts(10);
    }
    
    /**
     * Search products
     */
    public function search() {
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            echo json_encode(['error' => 'Search term too short']);
            return;
        }
        
        $products = $this->productModel->searchProducts($query);
        
        echo json_encode([
            'success' => true,
            'products' => $products
        ]);
    }
}
?>
