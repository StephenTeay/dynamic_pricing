<?php
// api/v1/orders.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/OrderItem.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../utils/logger.php';

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

Session::start();

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));
$orderId = $parts[3] ?? null;

$orderModel = new Order();

try {
    if ($method === 'GET') {
        if (!Session::isLoggedIn()) {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        if ($orderId && is_numeric($orderId)) {
            $order = $orderModel->find($orderId);
            
            if (!$order) {
                jsonResponse(['error' => 'Order not found'], 404);
            }
            
            if ($order['buyer_id'] != Session::getUserId() && 
                $order['seller_id'] != Session::getUserId()) {
                jsonResponse(['error' => 'Unauthorized'], 401);
            }
            
            jsonResponse(['data' => $order]);
            
        } else {
            $userId = Session::getUserId();
            $userType = Session::getUserType();
            
            if ($userType === 'seller') {
                $orders = $orderModel->getOrdersBySeller($userId);
            } else {
                $orders = $orderModel->getOrdersByBuyer($userId);
            }
            
            jsonResponse(['data' => $orders, 'count' => count($orders)]);
        }
        
    } elseif ($method === 'POST') {
        if (!Session::isLoggedIn() || Session::getUserType() !== 'buyer') {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['items'])) {
            jsonResponse(['error' => 'Order items required'], 400);
        }
        
        $orderData = [
            'buyer_id' => Session::getUserId(),
            'items' => $input['items'],
            'shipping_address' => $input['shipping_address'] ?? null,
            'notes' => $input['notes'] ?? null
        ];
        
        $orderId = $orderModel->createOrder($orderData);
        
        if ($orderId) {
            jsonResponse([
                'success' => true,
                'message' => 'Order created',
                'order_id' => $orderId
            ], 201);
        } else {
            jsonResponse(['error' => 'Failed to create order'], 500);
        }
        
    } elseif ($method === 'PUT') {
        if (!$orderId || !is_numeric($orderId)) {
            jsonResponse(['error' => 'Order ID required'], 400);
        }
        
        if (!Session::isLoggedIn() || Session::getUserType() !== 'seller') {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        $order = $orderModel->find($orderId);
        
        if (!$order) {
            jsonResponse(['error' => 'Order not found'], 404);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($orderModel->update($orderId, $input)) {
            jsonResponse(['success' => true, 'message' => 'Order updated']);
        } else {
            jsonResponse(['error' => 'Failed to update order'], 500);
        }
        
    } else {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
    
} catch (Exception $e) {
    Logger::exception($e);
    jsonResponse(['error' => 'Server error'], 500);
}
?>
