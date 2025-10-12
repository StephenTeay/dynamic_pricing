<?php
// services/NotificationService.php

require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../config/config.php';

class NotificationService {
    private $notificationModel;
    
    public function __construct() {
        $this->notificationModel = new Notification();
    }
    
    /**
     * Create notification
     */
    public function create($userId, $type, $message, $data = []) {
        return $this->notificationModel->createNotification([
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'data' => json_encode($data)
        ]);
    }
    
    /**
     * Send order notification
     */
    public function notifyOrderCreated($orderId, $userId) {
        return $this->create(
            $userId,
            NOTIFICATION_ORDER,
            "New order #$orderId has been created",
            ['order_id' => $orderId]
        );
    }
    
    /**
     * Send inventory notification
     */
    public function notifyLowStock($productId, $userId) {
        return $this->create(
            $userId,
            NOTIFICATION_INVENTORY,
            "Product #$productId has low stock",
            ['product_id' => $productId]
        );
    }
    
    /**
     * Send price notification
     */
    public function notifyPriceChange($productId, $userId, $oldPrice, $newPrice) {
        return $this->create(
            $userId,
            NOTIFICATION_PRICE,
            "Price changed from $oldPrice to $newPrice",
            ['product_id' => $productId, 'old_price' => $oldPrice, 'new_price' => $newPrice]
        );
    }
    
    /**
     * Get user notifications
     */
    public function getUserNotifications($userId) {
        return $this->notificationModel->getByUserId($userId);
    }
    
    /**
     * Mark as read
     */
    public function markAsRead($notificationId) {
        return $this->notificationModel->markAsRead($notificationId);
    }
}
?>
