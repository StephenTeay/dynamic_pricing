<?php
// utils/logger.php

class Logger {
    private static $logFile = __DIR__ . '/../logs/app.log';
    
    public static function log($message, $level = 'INFO') {
        self::ensureLogDirectory();
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message\n";
        
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }
    
    public static function info($message) {
        self::log($message, 'INFO');
    }
    
    public static function warning($message) {
        self::log($message, 'WARNING');
    }
    
    public static function error($message) {
        self::log($message, 'ERROR');
    }
    
    public static function exception($exception) {
        $message = $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine();
        self::log($message, 'EXCEPTION');
    }
    
    public static function apiRequest($method, $endpoint, $statusCode) {
        $message = "$method /api/{$endpoint} - Status: $statusCode";
        self::log($message, 'API');
    }
    
    private static function ensureLogDirectory() {
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
}
?>