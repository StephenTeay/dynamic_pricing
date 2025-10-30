<?php
// config/constants.php

// Application Configuration
define('BASE_PATH', '/dynamic/dynamic_pricing/public');
define('APP_NAME', 'Dynamic Pricing');

// User Types
define('USER_TYPE_BUYER', 'buyer');
define('USER_TYPE_SELLER', 'seller');
define('USER_TYPE_ADMIN', 'admin');

// Order Status
define('ORDER_STATUS_PENDING', 'pending');
define('ORDER_STATUS_CONFIRMED', 'confirmed');
define('ORDER_STATUS_PROCESSING', 'processing');
define('ORDER_STATUS_SHIPPED', 'shipped');
define('ORDER_STATUS_DELIVERED', 'delivered');
define('ORDER_STATUS_CANCELLED', 'cancelled');

// Payment Status
define('PAYMENT_STATUS_PENDING', 'pending');
define('PAYMENT_STATUS_PAID', 'paid');
define('PAYMENT_STATUS_FAILED', 'failed');
define('PAYMENT_STATUS_REFUNDED', 'refunded');

// Pricing Rule Types
define('PRICING_RULE_FIXED', 'fixed');
define('PRICING_RULE_PERCENTAGE', 'percentage');
define('PRICING_RULE_DYNAMIC', 'dynamic');

// Notification Types
define('NOTIFICATION_ORDER', 'order');
define('NOTIFICATION_INVENTORY', 'inventory');
define('NOTIFICATION_PRICE', 'price');
define('NOTIFICATION_SYSTEM', 'system');

?>
