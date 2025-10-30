<?php
// core/Session.php

class Session {
    const SESSION_USER_ID = 'user_id';
    const SESSION_USER_TYPE = 'user_type';
    const SESSION_USERNAME = 'username';
    const FLASH_KEY = 'flash';
    
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function login($userId, $userType, $username) {
        self::start();
        $_SESSION[self::SESSION_USER_ID] = $userId;
        $_SESSION[self::SESSION_USER_TYPE] = $userType;
        $_SESSION[self::SESSION_USERNAME] = $username;
    }
    
    public static function logout() {
        self::start();
        session_destroy();
    }
    
    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION[self::SESSION_USER_ID]);
    }
    
    public static function getUserId() {
        self::start();
        return $_SESSION[self::SESSION_USER_ID] ?? null;
    }
    
    public static function getUserType() {
        self::start();
        return $_SESSION[self::SESSION_USER_TYPE] ?? null;
    }
    
    public static function getUsername() {
        self::start();
        return $_SESSION[self::SESSION_USERNAME] ?? null;
    }
    
    public static function isSeller() {
        return self::getUserType() === 'seller';
    }
    
    public static function isBuyer() {
        return self::getUserType() === 'buyer';
    }
    
    public static function isAdmin() {
        return self::getUserType() === 'admin';
    }
    
    public static function setFlash($type, $message) {
        self::start();
        if (!isset($_SESSION[self::FLASH_KEY])) {
            $_SESSION[self::FLASH_KEY] = [];
        }
        $_SESSION[self::FLASH_KEY][$type] = $message;
    }
    
    public static function getFlash($type) {
        self::start();
        if (isset($_SESSION[self::FLASH_KEY][$type])) {
            $message = $_SESSION[self::FLASH_KEY][$type];
            unset($_SESSION[self::FLASH_KEY][$type]);
            return $message;
        }
        return null;
    }
    
    public static function hasFlash($type) {
        self::start();
        return isset($_SESSION[self::FLASH_KEY][$type]);
    }
}
?>
