<?php
// utils/url.php

if (!function_exists('url')) {
    function url($path = '') {
        if (defined('APP_BASE')) {
            $basePath = rtrim(APP_BASE, '/');
        } else {
            $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        }
        return $basePath . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset($path) {
        if (!$path) return '';
        if (defined('APP_BASE')) {
            $basePath = rtrim(APP_BASE, '/');
        } else {
            $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        }
        // If the path already starts with 'assets/', don't add it again
        if (strpos($path, 'assets/') === 0) {
            return $basePath . '/' . ltrim($path, '/');
        }
        return $basePath . '/assets/' . ltrim($path, '/');
    }
}
?>