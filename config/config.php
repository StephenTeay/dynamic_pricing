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

// Base paths
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('UPLOAD_DIR', PUBLIC_PATH . '/assets/images/uploads/');

// URLs
// Compute BASE_URL dynamically so the app works when placed in a subdirectory.
// Fallback to http://localhost when running from CLI or when server vars are not available.
if (php_sapi_name() === 'cli' || empty($_SERVER['HTTP_HOST'])) {
    // CLI environment or no host header available
    define('BASE_URL', 'http://localhost');
} else {
    // Determine the protocol
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';

    // Get host (may include port)
    $host = $_SERVER['HTTP_HOST'];

    // Determine base path from SCRIPT_NAME or PHP_SELF. The public entrypoint lives in /public,
    // so strip off the trailing /index.php or /public/index.php if present.
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    // If the app is served from the 'public' folder, we want the path up to that folder.
    // Example: /dynamic/dynamic_pricing/public -> keep /dynamic/dynamic_pricing/public
    $basePath = $scriptDir === '/' ? '' : $scriptDir;

    // Build BASE_URL
    define('BASE_URL', $scheme . '://' . $host . $basePath);
}

// Assets URL relative to BASE_URL
define('ASSETS_URL', rtrim(BASE_URL, '/') . '/assets');

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
require_once BASE_PATH . '/utils/helpers.php';

// Autoloader for classes
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/core/',
        BASE_PATH . '/models/',
        BASE_PATH . '/controllers/',
        BASE_PATH . '/services/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});