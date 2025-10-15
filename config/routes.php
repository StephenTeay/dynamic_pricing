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

    // (No extra duplicate dynamic-pricing prefixed routes)
    ],
    
    // POST routes
    'POST' => [
        // Auth routes
        '/auth/login' => 'AuthController@login',
        '/auth/register' => 'AuthController@register',
        '/auth/logout' => 'AuthController@logout',
        '/auth/forgot-password' => 'AuthController@forgotPassword',
        '/auth/reset-password' => 'AuthController@resetPassword',
        
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
    ],
  
    
    'DELETE' => [
        '/seller/product/:id' => 'SellerController@deleteProduct',
    ],
];
?>
