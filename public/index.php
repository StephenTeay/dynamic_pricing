<?php
// public/index.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../utils/helpers.php';

Session::start();

// Get the request URI and method
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];
$basePath = rtrim(BASE_PATH, '/');

// Debug raw request information
if (defined('APP_DEBUG') && APP_DEBUG) {
    error_log("Raw Request URI: " . $requestUri);
    error_log("SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
    error_log("PHP_SELF: " . $_SERVER['PHP_SELF']);
    error_log("DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT']);
}

// Extract base path from config
$configBasePath = rtrim(BASE_PATH, '/');

// Clean up the request URI by removing consecutive slashes
$requestUri = preg_replace('#/+#', '/', $requestUri);

// Remove any repetitions of the base path
$pattern = '#^' . preg_quote($configBasePath, '#') . '(?:/+' . preg_quote($configBasePath, '#') . ')*#';
$cleanUri = preg_replace($pattern, '', $requestUri);

// Ensure the URI starts with a single slash
$requestUri = '/' . ltrim($requestUri, '/');

// Extract the path after the base path
$relativePath = preg_replace('#^' . preg_quote($basePath, '#') . '#', '', $requestUri);
$relativePath = '/' . ltrim($relativePath, '/');

// If accessing the base URL with or without trailing slash, route to home page
if ($relativePath === '/' || $relativePath === '/public' || $relativePath === '/public/') {
    $relativePath = '/';
}

// Debug processed information
if (defined('APP_DEBUG') && APP_DEBUG) {
    error_log("Processed Request URI: " . $relativePath);
    error_log("Request Method: " . $requestMethod);
    error_log("Base Path: " . $basePath);
}

// Ensure $pageTitle exists to avoid warnings if this file is rendered directly
if (!isset($pageTitle)) {
    $pageTitle = APP_NAME . ' - Dynamic Pricing System';
}

// Load routes
$routes = require_once __DIR__ . '/../config/routes.php';

// Initialize router
$router = new Router();

// Register routes
foreach ($routes as $method => $routeList) {
    foreach ($routeList as $route => $handler) {
        $router->register($method, $route, $handler);
    }
}

// Handle the request
try {
    $router->dispatch($relativePath, $requestMethod);
    // Ensure we stop execution after the router handles the response
    exit;
} catch (Exception $e) {
    // Log the error to Apache/PHP log
    error_log("Router Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());

    // Also append to app error log for easier access
    $appLog = __DIR__ . '/../logs/error.log';
    $msg = sprintf("[%s] Router Exception: %s in %s on line %d\nStack: %s\n", date('Y-m-d H:i:s'), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
    error_log($msg, 3, $appLog);

    // Show error page
    http_response_code(500);
    require __DIR__ . '/../views/errors/500.php';
    exit;
}?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/buyer.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
        }
        
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .cta-btn {
            padding: 0.75rem 2rem;
            font-size: 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: transform 0.3s;
        }
        
        .cta-btn:hover {
            transform: translateY(-2px);
        }
        
        .cta-primary {
            background-color: white;
            color: #2563eb;
            font-weight: 600;
        }
        
        .cta-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            font-weight: 600;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1rem 0;">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 1.5rem; font-weight: bold; color: #2563eb;">
                <?php echo APP_NAME; ?>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                    <?php if (Session::isLoggedIn()): ?>
                    <?php if (Session::isSeller()): ?>
                        <a href="<?php echo BASE_URL; ?>/seller/dashboard" class="btn btn-primary">Dashboard</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/buyer/shop" class="btn btn-primary">Shop</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/auth/logout" class="btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login" class="btn btn-primary">Login</a>
                    <a href="<?php echo BASE_URL; ?>/register" class="btn btn-secondary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="hero">
        <div class="container">
            <h1>Dynamic Pricing System</h1>
            <p>Intelligent price management for modern e-commerce</p>
            
            <div class="cta-buttons">
                    <?php if (!Session::isLoggedIn()): ?>
                    <a href="<?php echo url('/register') . '?type=buyer'; ?>" class="cta-btn cta-primary">Shop Now</a>
                    <a href="<?php echo url('/register') . '?type=seller'; ?>" class="cta-btn cta-secondary">Become a Seller</a>
                <?php elseif (Session::isBuyer()): ?>
                    <a href="<?php echo url('/buyer/shop'); ?>" class="cta-btn cta-primary">Start Shopping</a>
                <?php else: ?>
                    <a href="<?php echo url('/seller/dashboard'); ?>" class="cta-btn cta-primary">Go to Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 3rem;">Why Choose Us?</h2>
        
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Dynamic Pricing</h3>
                <p>Automatically adjust prices based on demand, inventory, and market conditions.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <h3>Inventory Management</h3>
                <p>Real-time inventory tracking and automated low stock alerts.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📈</div>
                <h3>Analytics</h3>
                <p>Comprehensive analytics to track sales, revenue, and product performance.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Multi-Currency</h3>
                <p>Support for multiple currencies with real-time exchange rates.</p>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../views/layouts/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
</body>
</html>
