<?php
// config/config.php

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Application settings
define('APP_NAME', 'Dynamic Pricing System');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production
define('APP_DEBUG', true);

// Base paths - for filesystem operations
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_DIR', PUBLIC_PATH . '/assets/images/uploads/');

// Base paths
define('BASE_PATH', '/dynamic/dynamic_pricing/public'); // For routing/filesystem
define('APP_BASE', '/dynamic/dynamic_pricing/public'); // For URLs and assets

// URLs
// Compute BASE_URL dynamically so the app works when placed in a subdirectory.
if (!defined('BASE_URL')) {
    if (php_sapi_name() === 'cli' || empty($_SERVER['HTTP_HOST'])) {
        // CLI environment or no host header available
        define('BASE_URL', 'http://localhost' . BASE_PATH);
    } else {
        // Determine the protocol
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                 (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';

        // Get host (may include port)
        $host = $_SERVER['HTTP_HOST'];

        // Build BASE_URL using the constant BASE_PATH
        define('BASE_URL', $scheme . '://' . $host . BASE_PATH);
    }
}

// Include common utilities and helpers
require_once ROOT_PATH . '/utils/url.php';

// Assets URL relative to BASE_URL
if (!defined('ASSETS_URL')) {
    define('ASSETS_URL', rtrim(BASE_URL, '/') . '/assets');
}

// Security
define('SESSION_LIFETIME', 3600); // 1 hour
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);

// Database (loaded from database.php)
// See config/database.php

// File upload
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Pagination
define('PRODUCTS_PER_PAGE', 20);
define('ORDERS_PER_PAGE', 20);

// Currency
define('DEFAULT_CURRENCY', 'NGN');
define('SUPPORTED_CURRENCIES', ['NGN', 'USD', 'EUR', 'GBP']);

// Pricing
define('MIN_PROFIT_MARGIN', 0.10); // 10%
define('MAX_PRICE_INCREASE', 0.50); // 50%
define('MAX_PRICE_DECREASE', 0.30); // 30%

// Email (SMTP)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM_EMAIL', 'noreply@dynamicpricing.com');
define('SMTP_FROM_NAME', APP_NAME);

// Notification settings
define('ENABLE_EMAIL_NOTIFICATIONS', true);
define('ENABLE_SMS_NOTIFICATIONS', false);

// API settings
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // requests per hour

// Timezone
date_default_timezone_set('Africa/Lagos');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', APP_ENV === 'production' ? 1 : 0);

// Load helpers
require_once ROOT_PATH . '/utils/helpers.php';

// Autoloader for classes
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/core/',
        ROOT_PATH . '/models/',
        ROOT_PATH . '/controllers/',
        ROOT_PATH . '/services/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});