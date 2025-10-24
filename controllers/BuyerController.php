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
        if (!Session::isLoggedIn()) {
            redirect('/login');
            return;
        }

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
        if (!Session::isLoggedIn()) {
            redirect('/login');
            return;
        }

        $product = $this->productModel->findWithInventory($productId);
        
        if (!$product) {
            redirect('/buyer/shop');
            return;
        }

        // Get pricing history
        $db = (new Database())->getConnection();
        $historyStmt = $db->prepare(
            "SELECT * FROM pricing_history WHERE product_id = :product_id 
             ORDER BY changed_at DESC LIMIT 10"
        );
        $historyStmt->execute([':product_id' => $productId]);
        $priceHistory = $historyStmt->fetchAll();
        
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
        if (!Session::isLoggedIn()) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

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

    /**
     * View shopping cart
     */
    public function viewCart() {
        if (!Session::isLoggedIn()) {
            redirect('/login');
            return;
        }

        include __DIR__ . '/../views/buyer/cart.php';
    }

    /**
     * View buyer's orders
     */
    public function myOrders() {
        if (!Session::isLoggedIn()) {
            redirect('/login');
            return;
        }

        $userId = Session::getUserId();
        $orders = $this->orderModel->getOrdersByBuyer($userId);
        
        include __DIR__ . '/../views/buyer/orders.php';
    }

    /**
     * Process checkout
     */
    public function checkout() {
        if (!Session::isLoggedIn()) {
            redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['items']) || empty($input['items'])) {
                echo json_encode(['error' => 'Cart is empty']);
                return;
            }
            
            $orderId = $this->orderModel->createOrder([
                'buyer_id' => Session::getUserId(),
                'items' => $input['items']
            ]);
            
            if ($orderId) {
                echo json_encode([
                    'success' => true,
                    'order_id' => $orderId
                ]);
            } else {
                echo json_encode(['error' => 'Failed to create order']);
            }
            
            return;
        }
        
        include __DIR__ . '/../views/buyer/checkout.php';
    }
}
?>
