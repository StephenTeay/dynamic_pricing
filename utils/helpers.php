<?php
// utils/helpers.php

/**
 * Format currency
 */
function formatCurrency($amount, $currency = DEFAULT_CURRENCY) {
    $symbols = [
        'NGN' => '₦',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'JPY' => '¥'
    ];
    
    $symbol = $symbols[$currency] ?? $currency;
    $formatted = number_format($amount, 2);
    
    return "{$symbol}{$formatted}";
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    try {
        return date($format, strtotime($date));
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Redirect to URL
 */
function redirect($url) {
    // If a relative path is passed (not an absolute URL), prefix with BASE_URL
    if (!preg_match('/^https?:\/\//i', $url)) {
        $url = rtrim(BASE_URL, '/') . '/' . ltrim($url, '/');
    }
    header("Location: {$url}");
    exit;
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Get percentage change
 */
function getPercentageChange($oldValue, $newValue) {
    if ($oldValue == 0) {
        return 0;
    }
    return round((($newValue - $oldValue) / $oldValue) * 100, 2);
}

/**
 * Check if value is between range
 */
function isBetween($value, $min, $max) {
    return $value >= $min && $value <= $max;
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get status badge color
 */
function getStatusBadgeClass($status) {
    $classes = [
        'pending' => 'badge-pending',
        'confirmed' => 'badge-confirmed',
        'processing' => 'badge-confirmed',
        'shipped' => 'badge-shipped',
        'delivered' => 'badge-delivered',
        'cancelled' => 'badge-cancelled',
        'active' => 'badge-success',
        'inactive' => 'badge-danger',
        'low_stock' => 'badge-warning',
        'out_of_stock' => 'badge-danger'
    ];
    
    return $classes[$status] ?? 'badge';
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Check file size
 */
function getFileSizeFormatted($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Get client IP
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return filter_var($ip, FILTER_VALIDATE_IP) ?: 'UNKNOWN';
}

/**
 * Check if user is mobile
 */
function isMobileDevice() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return preg_match('/(android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini)/i', $userAgent);
}

/**
 * Array to query string
 */
function arrayToQueryString($data) {
    return http_build_query($data);
}

/**
 * Get time ago
 */
function getTimeAgo($datetime) {
    $time_ago = strtotime($datetime);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);
    
    if ($seconds <= 60) {
        return "Just now";
    } elseif ($minutes <= 60) {
        return ($minutes == 1) ? "1 minute ago" : "$minutes minutes ago";
    } elseif ($hours <= 24) {
        return ($hours == 1) ? "1 hour ago" : "$hours hours ago";
    } elseif ($days <= 7) {
        return ($days == 1) ? "1 day ago" : "$days days ago";
    } elseif ($weeks <= 4) {
        return ($weeks == 1) ? "1 week ago" : "$weeks weeks ago";
    } elseif ($months <= 12) {
        return ($months == 1) ? "1 month ago" : "$months months ago";
    } else {
        return ($years == 1) ? "1 year ago" : "$years years ago";
    }
}

/**
 * Generate slug from string
 */
function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Send email
 */
function sendEmail($to, $subject, $body, $headers = []) {
    $defaultHeaders = [
        'From: ' . SMTP_FROM_EMAIL,
        'Content-Type: text/html; charset=UTF-8'
    ];
    
    $headers = array_merge($defaultHeaders, $headers);
    $headerString = implode("\r\n", $headers);
    
    return mail($to, $subject, $body, $headerString);
}

/**
 * Log activity
 */
function logActivity($userId, $action, $details = '') {
    $logMessage = sprintf(
        "[%s] User ID: %d | Action: %s | Details: %s | IP: %s",
        date('Y-m-d H:i:s'),
        $userId,
        $action,
        $details,
        getClientIP()
    );
    
    error_log($logMessage . "\n", 3, __DIR__ . '/../logs/activity.log');
}

/**
 * Check permission
 */
function hasPermission($userId, $resource, $action) {
    // This is a basic implementation
    // Extend with your permission logic
    $db = (new Database())->getConnection();
    $query = "SELECT user_type FROM users WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return false;
    }
    
    // Admin has all permissions
    if ($user['user_type'] === 'admin') {
        return true;
    }
    
    // Add specific permission checks
    return false;
}

/**
 * Get error message
 */
function getErrorMessage($code) {
    $errors = [
        'INVALID_EMAIL' => 'Please enter a valid email address',
        'PASSWORD_MISMATCH' => 'Passwords do not match',
        'USER_EXISTS' => 'User already exists',
        'USER_NOT_FOUND' => 'User not found',
        'INVALID_PASSWORD' => 'Invalid password',
        'UNAUTHORIZED' => 'You are not authorized to perform this action',
        'FILE_TOO_LARGE' => 'File size exceeds limit',
        'INVALID_FILE_TYPE' => 'Invalid file type',
        'DATABASE_ERROR' => 'Database error occurred',
        'UNKNOWN_ERROR' => 'An unknown error occurred'
    ];
    
    return $errors[$code] ?? $errors['UNKNOWN_ERROR'];
}

/**
 * Create pagination links
 */
function createPaginationLinks($currentPage, $totalPages, $baseUrl) {
    $links = [];
    
    if ($currentPage > 1) {
        $links[] = '<a href="' . $baseUrl . '?page=1">First</a>';
        $links[] = '<a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '">Previous</a>';
    }
    
    for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
        if ($i === $currentPage) {
            $links[] = '<span class="active">' . $i . '</span>';
        } else {
            $links[] = '<a href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a>';
        }
    }
    
    if ($currentPage < $totalPages) {
        $links[] = '<a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '">Next</a>';
        $links[] = '<a href="' . $baseUrl . '?page=' . $totalPages . '">Last</a>';
    }
    
    return implode(' | ', $links);
}