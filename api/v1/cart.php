<?php
// api/v1/cart.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../utils/logger.php';

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

Session::start();

$method = $_SERVER['REQUEST_METHOD'];

try {
    $productModel = new Product();
    
    if ($method === 'GET') {
        // Return up-to-date product information for cart items
        $productIds = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
        
        if (empty($productIds)) {
            jsonResponse(['data' => []]);
            return;
        }
        
        // Sanitize product IDs
        $productIds = array_map(function($id) {
            return filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        }, $productIds);
        
        $products = [];
        foreach ($productIds as $id) {
            if (!empty($id)) {
                $product = $productModel->findWithInventory($id);
                if ($product) {
                    // Only include necessary fields for cart display
                    $products[] = [
                        'id' => (int)$product['product_id'],
                        'name' => $product['product_name'],
                        'price' => (float)$product['current_price'],
                        'image_url' => $product['image_url'],
                        'sku' => $product['sku'],
                        'quantity_available' => (int)$product['quantity_available'],
                        'seller_name' => $product['business_name']
                    ];
                }
            }
        }
        
        jsonResponse([
            'success' => true,
            'data' => $products
        ]);
        
    } elseif ($method === 'POST') {
        // Validate cart items before checkout
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['items']) || !is_array($input['items'])) {
            jsonResponse(['error' => 'Invalid cart data'], 400);
        }
        
        $validatedItems = [];
        $errors = [];
        
        foreach ($input['items'] as $item) {
            if (!isset($item['id']) || !isset($item['quantity'])) {
                $errors[] = 'Invalid item format';
                continue;
            }
            
            $product = $productModel->findWithInventory($item['id']);
            
            if (!$product) {
                $errors[] = "Product {$item['id']} not found";
                continue;
            }
            
            if ($product['inventory_count'] < $item['quantity']) {
                $errors[] = "Insufficient inventory for product {$product['name']}";
                continue;
            }
            
            $validatedItems[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['current_price'],
                'quantity' => $item['quantity'],
                'inventory_count' => $product['inventory_count']
            ];
        }
        
        if (!empty($errors)) {
            jsonResponse([
                'success' => false,
                'errors' => $errors
            ], 400);
        }
        
        jsonResponse([
            'success' => true,
            'data' => $validatedItems
        ]);
        
    } else {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
    
} catch (Exception $e) {
    Logger::exception($e);
    jsonResponse(['error' => 'Server error'], 500);
}
?>