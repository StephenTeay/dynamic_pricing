<?php
// controllers/CartController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../core/Response.php';

class CartController extends Controller {
    private $productModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
    }
    
    public function getCartItems() {
        try {
            // Get product IDs from query string
            $productIds = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
            
            if (empty($productIds)) {
                Response::json(['success' => true, 'data' => []]);
                return;
            }
            
            // Sanitize product IDs
            $productIds = array_map(function($id) {
                return filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            }, array_filter($productIds));
            
            if (empty($productIds)) {
                Response::json(['success' => true, 'data' => []]);
                return;
            }
            
            $products = [];
            foreach ($productIds as $id) {
                try {
                    $product = $this->productModel->findWithInventory($id);
                    if ($product) {
                        // Only include necessary fields for cart display
                        $products[] = [
                            'id' => (int)$product['product_id'],
                            'name' => $product['product_name'],
                            'current_price' => (float)$product['current_price'],
                            'image_url' => $product['image_url'],
                            'sku' => $product['sku'],
                            'quantity_available' => (int)$product['quantity_available'],
                            'seller_name' => $product['business_name']
                        ];
                    }
                } catch (Exception $e) {
                    error_log("Error fetching product $id: " . $e->getMessage());
                    continue;
                }
            }
            
            Response::json([
                'success' => true,
                'data' => $products
            ]);
            
        } catch (Exception $e) {
            error_log("CartController::getCartItems error: " . $e->getMessage());
            Response::json([
                'success' => false,
                'error' => 'Failed to fetch cart items'
            ], 500);
        }
    }
    
    public function validateCart() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['items']) || !is_array($input['items'])) {
                Response::json([
                    'success' => false,
                    'error' => 'Invalid cart data'
                ], 400);
                return;
            }
            
            $validatedItems = [];
            $errors = [];
            
            foreach ($input['items'] as $item) {
                if (!isset($item['id']) || !isset($item['quantity'])) {
                    $errors[] = 'Invalid item format';
                    continue;
                }
                
                $product = $this->productModel->findWithInventory($item['id']);
                
                if (!$product) {
                    $errors[] = "Product {$item['id']} not found";
                    continue;
                }
                
                if ($product['quantity_available'] < $item['quantity']) {
                    $errors[] = "Insufficient inventory for {$product['product_name']}";
                    continue;
                }
                
                $validatedItems[] = [
                    'id' => (int)$product['product_id'],
                    'name' => $product['product_name'],
                    'current_price' => (float)$product['current_price'],
                    'quantity' => (int)$item['quantity'],
                    'quantity_available' => (int)$product['quantity_available']
                ];
            }
            
            if (!empty($errors)) {
                Response::json([
                    'success' => false,
                    'errors' => $errors
                ], 400);
                return;
            }
            
            Response::json([
                'success' => true,
                'data' => $validatedItems
            ]);
            
        } catch (Exception $e) {
            error_log("CartController::validateCart error: " . $e->getMessage());
            Response::json([
                'success' => false,
                'error' => 'Failed to validate cart'
            ], 500);
        }
    }
}