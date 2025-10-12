<?php
// controllers/OrderController.php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../core/Session.php';

class OrderController {
    private $orderModel;
    private $orderItemModel;
    private $inventoryModel;
    
    public function __construct() {
        $this->orderModel = new Order();
        $this->orderItemModel = new OrderItem();
        $this->inventoryModel = new Inventory();
    }
    
    /**
     * Create new order
     */
    public function create($data) {
        if (!Session::isLoggedIn() || Session::getUserType() !== 'buyer') {
            return false;
        }
        
        $orderId = $this->orderModel->createOrder([
            'buyer_id' => Session::getUserId()
        ]);
        
        if (!$orderId) {
            return false;
        }
        
        // Add order items
        $total = 0;
        foreach ($data['items'] as $item) {
            $this->orderItemModel->createItem([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity']
            ]);
            
            $total += $item['price'] * $item['quantity'];
        }
        
        // Update order total
        $this->orderModel->update($orderId, ['total_amount' => $total]);
        
        return $orderId;
    }
    
    /**
     * Get buyer's orders
     */
    public function getBuyerOrders() {
        return $this->orderModel->getOrdersByBuyer(Session::getUserId());
    }
    
    /**
     * Get seller's orders
     */
    public function getSellerOrders() {
        return $this->orderModel->getOrdersBySeller(Session::getUserId());
    }
    
    /**
     * Get order details
     */
    public function getOrderDetails($orderId) {
        $order = $this->orderModel->find($orderId);
        
        if (!$order) {
            return null;
        }
        
        $order['items'] = $this->orderItemModel->getByOrderId($orderId);
        
        return $order;
    }
    
    /**
     * Update order status
     */
    public function updateStatus($orderId, $status) {
        $order = $this->orderModel->find($orderId);
        
        if (!$order) {
            return false;
        }
        
        if ($order['seller_id'] != Session::getUserId()) {
            return false;
        }
        
        return $this->orderModel->update($orderId, ['order_status' => $status]);
    }
    
    /**
     * Cancel order
     */
    public function cancel($orderId) {
        $order = $this->orderModel->find($orderId);
        
        if (!$order || $order['buyer_id'] != Session::getUserId()) {
            return false;
        }
        
        return $this->orderModel->update($orderId, ['order_status' => 'cancelled']);
    }
}
?>
