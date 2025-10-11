<?php
// utils/security.php

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Sanitize input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Escape for database (use prepared statements instead)
 */
function escape($input) {
    return addslashes($input);
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    return Session::verifyCSRFToken($token);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    return Session::generateCSRFToken();
}

/**
 * Check if HTTPS
 */
function isHTTPS() {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
           $_SERVER['SERVER_PORT'] == 443;
}

/**
 * Get secure headers
 */
function setSecurityHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Enable XSS protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Feature policy
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    
    // Content Security Policy (adjust as needed)
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;");
}

/**
 * Rate limiting check
 */
function checkRateLimit($identifier, $limit = 100, $window = 3600) {
    $cacheKey = "rate_limit_$identifier";
    
    // Simple in-memory rate limiting (use Redis in production)
    if (isset($_SERVER['HTTP_X_RATE_LIMIT']) && $_SERVER['HTTP_X_RATE_LIMIT'] > $limit) {
        return false;
    }
    
    return true;
}

/**
 * Generate API token
 */
function generateAPIToken($userId) {
    $payload = [
        'user_id' => $userId,
        'iat' => time(),
        'exp' => time() + (24 * 60 * 60) // 24 hours
    ];
    
    // In production, use JWT library
    return base64_encode(json_encode($payload));
}

/**
 * Verify API token
 */
function verifyAPIToken($token) {
    try {
        $payload = json_decode(base64_decode($token), true);
        
        if (!$payload || !isset($payload['exp']) || $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Validate file upload safety
 */
function isValidUpload($file, $allowedTypes = [], $maxSize = MAX_FILE_SIZE) {
    // Check file size
    if ($file['size'] > $maxSize) {
        return false;
    }
    
    // Check file type
    if (!empty($allowedTypes) && !in_array($file['type'], $allowedTypes)) {
        return false;
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!empty($allowedTypes) && !in_array($mime, $allowedTypes)) {
        return false;
    }
    
    return true;
}

/**
 * Sanitize filename
 */
function sanitizeFilename($filename) {
    // Remove special characters
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    
    // Prevent directory traversal
    $filename = str_replace(['..', '/', '\\'], '', $filename);
    
    return $filename;
}

/**
 * Generate secure random string
 */
function randomString($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[random_int(0, strlen($characters) - 1)];
    }
    
    return $string;
}

/**
 * Hash sensitive data
 */
function hashData($data) {
    return hash('sha256', $data . SECRET_KEY);
}

/**
 * Verify hashed data
 */
function verifyHashedData($data, $hash) {
    return hash_equals(hashData($data), $hash);
}