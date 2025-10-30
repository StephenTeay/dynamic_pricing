<?php
// config/routes.php

/**
 * Application Routes
 * Define all routes for the application
 */

return [
    // GET routes
    'GET' => [
        // Public routes
        '/' => 'HomeController@index',
        '/login' => 'AuthController@showLogin',
        '/auth/logout' => 'AuthController@logout',
        '/register' => 'AuthController@showRegister',
        '/forgot-password' => 'AuthController@showForgotPassword',
        '/auth/change-password' => 'AuthController@showChangePassword',
        '/auth/notifications' => 'AuthController@notifications',

        // Buyer routes
        '/buyer/shop' => 'BuyerController@shop',
        '/buyer/product/{id}' => 'BuyerController@productDetail',
        '/buyer/cart' => 'BuyerController@viewCart',
        '/buyer/checkout' => 'BuyerController@checkout',
        '/buyer/orders' => 'BuyerController@myOrders',

        // Seller routes
        '/seller/dashboard' => 'SellerController@dashboard',
        '/seller/products' => 'SellerController@products',
        '/seller/product/create' => 'SellerController@createProductForm',
        '/seller/product/edit/{id}' => 'SellerController@editProductForm',
        '/seller/inventory' => 'SellerController@inventory',
        '/seller/pricing' => 'SellerController@pricing',
        '/seller/orders' => 'SellerController@orders',
        '/seller/analytics' => 'SellerController@analytics',
        '/seller/settings' => 'SellerController@settings',

        // API Routes
        '/api/v1/cart' => 'CartController@getCartItems',
        '/api/v1/products/{id}' => 'ProductController@getProduct',
        '/api/v1/orders' => 'OrderController@getOrders',
        '/api/v1/orders/{id}' => 'OrderController@getOrder'
    ],
    
    // POST routes
    'POST' => [
        // Auth routes
        '/auth/login' => 'AuthController@login',
        '/auth/register' => 'AuthController@register',
        '/auth/logout' => 'AuthController@logout',
        '/auth/forgot-password' => 'AuthController@forgotPassword',
        '/auth/reset-password' => 'AuthController@resetPassword',
        '/auth/change-password' => 'AuthController@changePassword',
        '/auth/mark-notification-read' => 'AuthController@markNotificationRead',
        
        // Buyer routes
        '/buyer/cart/add' => 'BuyerController@addToCart',
        '/buyer/cart/update' => 'BuyerController@updateCart',
        '/buyer/cart/remove' => 'BuyerController@removeFromCart',
        '/buyer/order/create' => 'BuyerController@createOrder',
        
        // Seller routes
        '/seller/product/store' => 'SellerController@storeProduct',
        '/seller/product/update/{id}' => 'SellerController@updateProduct',
        '/seller/inventory/update' => 'SellerController@updateInventory',
        '/seller/pricing/update' => 'SellerController@updatePrice',
        '/seller/settings/update' => 'SellerController@updateSettings',

        // API Routes
        '/api/v1/cart' => 'api/v1/cart.php',
        '/api/v1/orders' => 'api/v1/orders.php'
    ],
    
    'DELETE' => [
        '/seller/product/{id}' => 'SellerController@deleteProduct'
    ],

    'PUT' => [
        '/api/v1/orders/{id}' => 'api/v1/orders.php'
    ]
];
?>
