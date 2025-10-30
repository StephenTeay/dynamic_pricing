<?php
// api/v1/analytics.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Order.php';
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
$metric = $parts[3] ?? null;

$orderModel = new Order();
$productModel = new Product();

try {
    if ($method === 'GET') {
        if (!Session::isLoggedIn() || Session::getUserType() !== 'seller') {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        $sellerId = Session::getUserId();
        $period = $_GET['period'] ?? 'month';
        
        if ($metric === 'revenue') {
            // Get revenue statistics
            $stats = $orderModel->getRevenueStats($sellerId, $period);
            jsonResponse(['data' => $stats]);
            
        } elseif ($metric === 'orders') {
            // Get order statistics by status
            $stats = $orderModel->getOrderStatsByStatus($sellerId);
            jsonResponse(['data' => $stats]);
            
        } elseif ($metric === 'products') {
            // Get product statistics
            $query = "SELECT 
                        COUNT(*) as total_products,
                        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_products,
                        COUNT(CASE WHEN quantity_available = 0 THEN 1 END) as out_of_stock,
                        COALESCE(AVG(current_price), 0) as average_price,
                        MAX(current_price) as max_price,
                        MIN(current_price) as min_price
                      FROM products p
                      LEFT JOIN inventory i ON p.product_id = i.product_id
                      WHERE p.seller_id = :seller_id";
            
            $db = (new Database())->getConnection();
            $stmt = $db->prepare($query);
            $stmt->execute([':seller_id' => $sellerId]);
            $stats = $stmt->fetch();
            
            jsonResponse(['data' => $stats]);
            
        } elseif ($metric === 'sales') {
            // Get daily sales data
            $query = "SELECT 
                        DATE(created_at) as date,
                        COUNT(*) as order_count,
                        SUM(total_amount) as total_revenue,
                        COUNT(CASE WHEN order_status = 'delivered' THEN 1 END) as completed_orders
                      FROM orders
                      WHERE seller_id = :seller_id
                      AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                      GROUP BY DATE(created_at)
                      ORDER BY date DESC";
            
            $db = (new Database())->getConnection();
            $stmt = $db->prepare($query);
            $stmt->execute([':seller_id' => $sellerId]);
            $data = $stmt->fetchAll();
            
            jsonResponse(['data' => $data]);
            
        } elseif ($metric === 'trending') {
            // Get trending products
            $query = "SELECT p.*, 
                             COUNT(oi.order_item_id) as order_count,
                             SUM(oi.subtotal) as revenue
                      FROM products p
                      LEFT JOIN order_items oi ON p.product_id = oi.product_id
                      WHERE p.seller_id = :seller_id
                      AND oi.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                      GROUP BY p.product_id
                      ORDER BY order_count DESC
                      LIMIT 10";
            
            $db = (new Database())->getConnection();
            $stmt = $db->prepare($query);
            $stmt->execute([':seller_id' => $sellerId]);
            $data = $stmt->fetchAll();
            
            jsonResponse(['data' => $data]);
            
        } elseif ($metric === 'pricing') {
            // Get pricing change statistics
            $query = "SELECT 
                        p.product_id,
                        p.product_name,
                        COUNT(*) as change_count,
                        AVG(price_change_percent) as avg_change_percent,
                        MAX(changed_at) as last_change
                      FROM pricing_history ph
                      INNER JOIN products p ON ph.product_id = p.product_id
                      WHERE p.seller_id = :seller_id
                      AND ph.changed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                      GROUP BY p.product_id
                      ORDER BY change_count DESC
                      LIMIT 10";
            
            $db = (new Database())->getConnection();
            $stmt = $db->prepare($query);
            $stmt->execute([':seller_id' => $sellerId]);
            $data = $stmt->fetchAll();
            
            jsonResponse(['data' => $data]);
            
        } else {
            // Get overall dashboard analytics
            $revenueStats = $orderModel->getRevenueStats($sellerId, $period);
            $orderStats = $orderModel->getOrderStatsByStatus($sellerId);
            $lowStockProducts = $productModel->getLowStockProducts($sellerId);
            
            jsonResponse([
                'revenue' => $revenueStats,
                'orders' => $orderStats,
                'low_stock_count' => count($lowStockProducts)
            ]);
        }
        
    } else {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
    
} catch (Exception $e) {
    Logger::exception($e);
    jsonResponse(['error' => 'Server error'], 500);
}