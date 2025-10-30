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
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        
        body {
            background: #f8fafc;
        }
        
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6rem 2rem;
            text-align: center;
            margin-bottom: 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }
        
        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -1px;
            text-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .hero p {
            font-size: 1.375rem;
            margin-bottom: 3rem;
            opacity: 0.95;
            font-weight: 500;
            line-height: 1.6;
        }
        
        .cta-buttons {
            display: flex;
            gap: 1.25rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .cta-btn {
            padding: 1rem 2.5rem;
            font-size: 1.0625rem;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        }
        
        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        
        .cta-primary {
            background: white;
            color: #667eea;
        }
        
        .cta-secondary {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 2px solid white;
            backdrop-filter: blur(10px);
        }
        
        .features-section {
            padding: 5rem 2rem;
            background: white;
            position: relative;
        }
        
        .features-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to bottom, #f8fafc, white);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }
        
        .section-title h2 {
            font-size: 2.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.75rem;
            letter-spacing: -1px;
        }
        
        .section-subtitle {
            color: #64748b;
            font-size: 1.125rem;
            margin-bottom: 4rem;
            font-weight: 500;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1.25rem;
            box-shadow: 0 4px 6px rgba(15, 23, 42, 0.07), 0 2px 4px rgba(15, 23, 42, 0.06);
            border: 2px solid #f1f5f9;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px rgba(15, 23, 42, 0.1), 0 10px 10px rgba(15, 23, 42, 0.04);
            border-color: rgba(102, 126, 234, 0.3);
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .feature-card h3 {
            font-size: 1.375rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }
        
        .feature-card p {
            color: #64748b;
            line-height: 1.7;
            font-size: 0.9375rem;
        }
        
        .stats-section {
            padding: 5rem 2rem;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            max-width: 1000px;
            margin: 3rem auto 0;
        }
        
        .stat-item {
            animation: fadeInUp 0.8s ease-out backwards;
        }
        
        .stat-item:nth-child(1) { animation-delay: 0.1s; }
        .stat-item:nth-child(2) { animation-delay: 0.2s; }
        .stat-item:nth-child(3) { animation-delay: 0.3s; }
        .stat-item:nth-child(4) { animation-delay: 0.4s; }
        
        .stat-number {
            font-size: 3.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #64748b;
            font-weight: 600;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.25rem;
            }
            
            .hero p {
                font-size: 1.125rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav style="background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.25rem 0; position: sticky; top: 0; z-index: 100; backdrop-filter: blur(10px);">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; max-width: 1280px; margin: 0 auto; padding: 0 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0.625rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <span style="font-size: 1.5rem; font-weight: 800; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    <?php echo APP_NAME; ?>
                </span>
            </div>
            
            <div style="display: flex; gap: 1rem; align-items: center;">
                <?php if (Session::isLoggedIn()): ?>
                    <?php if (Session::isSeller()): ?>
                        <a href="<?php echo BASE_URL; ?>/seller/dashboard" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/buyer/shop" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            Shop
                        </a>
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
        <div class="hero-content">
            <h1>Dynamic Pricing System</h1>
            <p>Intelligent price management for modern e-commerce. Maximize revenue with AI-powered pricing strategies.</p>
            
            <div class="cta-buttons">
                <?php if (!Session::isLoggedIn()): ?>
                    <a href="<?php echo url('/register') . '?type=buyer'; ?>" class="cta-btn cta-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        Shop Now
                    </a>
                    <a href="<?php echo url('/register') . '?type=seller'; ?>" class="cta-btn cta-secondary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        Become a Seller
                    </a>
                <?php elseif (Session::isBuyer()): ?>
                    <a href="<?php echo url('/buyer/shop'); ?>" class="cta-btn cta-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        Start Shopping
                    </a>
                <?php else: ?>
                    <a href="<?php echo url('/seller/dashboard'); ?>" class="cta-btn cta-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                        Go to Dashboard
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="features-section">
        <div class="container" style="max-width: 1280px; margin: 0 auto;">
            <div class="section-title">
                <h2>Why Choose Us?</h2>
                <p class="section-subtitle">Powerful features designed to grow your business</p>
            </div>
            
            <div class="features">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <h3>Dynamic Pricing</h3>
                    <p>Automatically adjust prices based on demand, inventory levels, and real-time market conditions to maximize revenue.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                    </div>
                    <h3>Inventory Management</h3>
                    <p>Real-time inventory tracking with automated low stock alerts and intelligent restocking recommendations.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <h3>Advanced Analytics</h3>
                    <p>Comprehensive analytics dashboard to track sales trends, revenue metrics, and product performance in real-time.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                    </div>
                    <h3>Multi-Currency</h3>
                    <p>Support for multiple currencies with real-time exchange rates and automatic conversion for global reach.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="stats-section">
        <div class="container" style="max-width: 1280px; margin: 0 auto;">
            <div class="section-title">
                <h2>Trusted by Businesses Worldwide</h2>
                <p class="section-subtitle">Join thousands of sellers growing their revenue</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">Active Sellers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500K+</div>
                    <div class="stat-label">Products Listed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">$2M+</div>
                    <div class="stat-label">Revenue Generated</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">Uptime</div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../views/layouts/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
</body>
</html>