<?php
// cron/send_notifications.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/NotificationService.php';
require_once __DIR__ . '/../models/Notification.php';

Logger::info('Starting notification cron job');

try {
    $notificationModel = new Notification();
    
    // Get unread notifications (if you want to process them)
    // This can be extended based on your business logic
    
    Logger::info('Notification cron job completed');
    
} catch (Exception $e) {
    Logger::error('Notification cron job failed: ' . $e->getMessage());
}
?>
