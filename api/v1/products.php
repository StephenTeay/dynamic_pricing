<?php
// api/v1/products.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../models/Product.php';

header('Content-Type: application/json');
Session::start();

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Parse request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));
$productId = end($parts);

if ($productId === 'products.php' || $productId === 'products') {
    $productId = null;
}

$productModel = new Product();

try {
    if ($method === 'GET') {
        if ($productId && is_numeric($productId)) {
            // Get single product
            $product = $productModel->find($productId);
            if (!$product) {
                jsonResponse(['error' => 'Product not found'], 404);
            }
            jsonResponse(['data' => $product]);
        } else {
            // Get all products
            $category = $_GET['category'] ?? null;
            $search = $_GET['search'] ?? null;
            $limit = (int)($_GET['limit'] ?? 20);
            $offset = (int)($_GET['offset'] ?? 0);
            
            if ($search) {
                $products = $productModel->searchProducts($search, $category);
            } elseif ($category) {
                $products = $productModel->getProductsByCategory($category, $limit);
            } else {
                $products = $productModel->findAll(['is_active' => 1], 'created_at DESC', $limit, $offset);
            }
            
            jsonResponse(['data' => $products, 'count' => count($products)]);
        }
    } 
    elseif ($method === 'POST') {
        if (!Session::isLoggedIn()) {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['product_name'], $data['base_cost'], $data['current_price'])) {
            jsonResponse(['error' => 'Missing required fields'], 400);
        }
        
        $id = $productModel->create($data);
        if ($id) {
            jsonResponse(['success' => true, 'id' => $id], 201);
        } else {
            jsonResponse(['error' => 'Failed to create product'], 500);
        }
    }
    elseif ($method === 'PUT') {
        if (!$productId || !is_numeric($productId)) {
            jsonResponse(['error' => 'Product ID required'], 400);
        }
        
        if (!Session::isLoggedIn()) {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($productModel->update($productId, $data)) {
            jsonResponse(['success' => true]);
        } else {
            jsonResponse(['error' => 'Failed to update product'], 500);
        }
    }
    elseif ($method === 'DELETE') {
        if (!$productId || !is_numeric($productId)) {
            jsonResponse(['error' => 'Product ID required'], 400);
        }
        
        if (!Session::isLoggedIn()) {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        if ($productModel->delete($productId)) {
            jsonResponse(['success' => true]);
        } else {
            jsonResponse(['error' => 'Failed to delete product'], 500);
        }
    }
    else {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}