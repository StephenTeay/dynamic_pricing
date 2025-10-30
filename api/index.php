<?php
// api/index.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../utils/security.php';
require_once __DIR__ . '/../utils/logger.php';

// Set security headers
setSecurityHeaders();

// Set JSON response header
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove /api prefix
$path = str_replace('/api', '', $path);
$parts = explode('/', trim($path, '/'));

// Router
$version = $parts[0] ?? '';
$endpoint = $parts[1] ?? '';
$action = $parts[2] ?? '';

// Check version
if ($version !== 'v1') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid API version']);
    exit;
}

// Log API request
Logger::apiRequest($method, $endpoint, 200);

// Route to appropriate handler
try {
    switch ($endpoint) {
        case 'auth':
            require_once __DIR__ . '/v1/auth.php';
            break;
            
        case 'products':
            require_once __DIR__ . '/v1/products.php';
            break;
            
        case 'orders':
            require_once __DIR__ . '/v1/orders.php';
            break;
            
        case 'inventory':
            require_once __DIR__ . '/v1/inventory.php';
            break;
            
        case 'pricing':
            require_once __DIR__ . '/v1/pricing.php';
            break;
            
        case 'analytics':
            require_once __DIR__ . '/v1/analytics.php';
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            exit;
    }
} catch (Exception $e) {
    Logger::exception($e);
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
    exit;
}