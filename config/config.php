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
define('BASE_URL', 'http://localhost');
define('ASSETS_URL', BASE_URL . '/assets');

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