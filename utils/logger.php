<?php
// utils/logger.php

class Logger {
    private static $logDir = __DIR__ . '/../logs/';
    
    /**
     * Initialize logger
     */
    public static function init() {
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
    }
    
    /**
     * Log debug message
     */
    public static function debug($message, $context = []) {
        self::log(LOG_LEVEL_DEBUG, $message, $context);
    }
    
    /**
     * Log info message
     */
    public static function info($message, $context = []) {
        self::log(LOG_LEVEL_INFO, $message, $context);
    }
    
    /**
     * Log warning message
     */
    public static function warning($message, $context = []) {
        self::log(LOG_LEVEL_WARNING, $message, $context);
    }
    
    /**
     * Log error message
     */
    public static function error($message, $context = []) {
        self::log(LOG_LEVEL_ERROR, $message, $context);
    }
    
    /**
     * Log critical message
     */
    public static function critical($message, $context = []) {
        self::log(LOG_LEVEL_CRITICAL, $message, $context);
    }
    
    /**
     * Generic log method
     */
    private static function log($level, $message, $context = []) {
        self::init();
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message";
        
        if (!empty($context)) {
            $logMessage .= ' | Context: ' . json_encode($context);
        }
        
        $logMessage .= " | IP: " . (self::getClientIP()) . "\n";
        
        // Write to appropriate log file
        $logFile = self::$logDir . strtolower($level) . '.log';
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        // Also write to general log
        file_put_contents(self::$logDir . 'app.log', $logMessage, FILE_APPEND);
    }
    
    /**
     * Get client IP
     */
    private static function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }
    }
    
    /**
     * Log user activity
     */
    public static function activity($userId, $action, $details = '') {
        $message = "User Activity: User ID $userId | Action: $action";
        self::info($message, ['details' => $details, 'user_id' => $userId]);
    }
    
    /**
     * Log API request
     */
    public static function apiRequest($method, $endpoint, $statusCode, $responseTime = 0) {
        $message = "API Request: $method $endpoint | Status: $statusCode | Time: {$responseTime}ms";
        self::info($message, [
            'method' => $method,
            'endpoint' => $endpoint,
            'status_code' => $statusCode
        ]);
    }
    
    /**
     * Log database query
     */
    public static function query($query, $executionTime = 0) {
        if (APP_ENV !== 'production') {
            self::debug("Database Query: $query | Time: {$executionTime}ms");
        }
    }
    
    /**
     * Log error with stack trace
     */
    public static function exception(Exception $e) {
        $message = "Exception: " . $e->getMessage();
        self::error($message, [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
    
    /**
     * Get log file contents
     */
    public static function getLog($logFile = 'app.log', $limit = 100) {
        self::init();
        
        $file = self::$logDir . $logFile;
        
        if (!file_exists($file)) {
            return [];
        }
        
        $lines = file($file);
        return array_slice(array_reverse($lines), 0, $limit);
    }
    
    /**
     * Clear log file
     */
    public static function clearLog($logFile = 'app.log') {
        self::init();
        
        $file = self::$logDir . $logFile;
        
        if (file_exists($file)) {
            file_put_contents($file, '');
        }
    }
    
    /**
     * Archive old logs
     */
    public static function archive() {
        self::init();
        
        $files = scandir(self::$logDir);
        $archiveDir = self::$logDir . 'archive/';
        
        if (!is_dir($archiveDir)) {
            mkdir($archiveDir, 0755, true);
        }
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $filePath = self::$logDir . $file;
            
            if (is_file($filePath)) {
                $fileSize = filesize($filePath);
                
                // Archive if larger than 10MB
                if ($fileSize > 10 * 1024 * 1024) {
                    $archiveFile = $archiveDir . $file . '.' . time();
                    rename($filePath, $archiveFile);
                    
                    // Recreate empty log file
                    touch($filePath);
                }
            }
        }
    }
}