<?php
// api/v1/inventory.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Inventory.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../core/Session.php';

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

Session::start();

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));
$productId = $parts[3] ?? null;

$inventoryModel = new Inventory();
$productModel = new Product();

try {
    if ($method === 'GET') {
        if ($productId && is_numeric($productId)) {
            // Get inventory for specific product
            $inventory = $inventoryModel->getByProductId($productId);
            
            if (!$inventory) {
                jsonResponse(['error' => 'Inventory not found'], 404);
            }
            
            jsonResponse(['data' => $inventory]);
        } else {
            // Get all inventory for seller
            if (!Session::isLoggedIn() || Session::getUserType() !== 'seller') {
                jsonResponse(['error' => 'Unauthorized'], 401);
            }
            
            $sellerId = $_GET['seller_id'] ?? null;
            if (!$sellerId) {
                jsonResponse(['error' => 'Seller ID required'], 400);
            }
            
            $products = $productModel->getProductsWithInventory($sellerId);
            jsonResponse(['data' => $products, 'count' => count($products)]);
        }
        
    } elseif ($method === 'PUT') {
        if (!$productId || !is_numeric($productId)) {
            jsonResponse(['error' => 'Product ID required'], 400);
        }
        
        if (!Session::isLoggedIn() || Session::getUserType() !== 'seller') {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        $product = $productModel->find($productId);
        
        if (!$product) {
            jsonResponse(['error' => 'Product not found'], 404);
        }
        
        // Check authorization
        if ($product['seller_id'] != Session::getUserId()) {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['quantity'])) {
            $result = $inventoryModel->updateStock($productId, $input['quantity']);
            
            if (!$result) {
                jsonResponse(['error' => 'Failed to update inventory'], 500);
            }
        }
        
        if (isset($input['adjustment'])) {
            $result = $inventoryModel->adjustStock($productId, $input['adjustment']);
            
            if (!$result) {
                jsonResponse(['error' => 'Failed to adjust inventory'], 500);
            }
        }
        
        jsonResponse(['success' => true, 'message' => 'Inventory updated']);
        
    } else {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
    
} catch (Exception $e) {
    Logger::exception($e);
    jsonResponse(['error' => 'Server error'], 500);
}