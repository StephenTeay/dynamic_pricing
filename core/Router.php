<?php
// core/Router.php

class Router {
    private $routes = [];
    private $currentMethod = '';
    private $currentPath = '';
    private $viewsPath;
    private $layoutsPath;
    
    public function __construct() {
        $this->viewsPath = __DIR__ . '/../views';
        $this->layoutsPath = $this->viewsPath . '/layouts';
    }
    
    public function register($method, $path, $handler) {
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }
        
        $this->routes[$method][$path] = $handler;
        return $this;
    }
    
    public function get($path, $handler) {
        return $this->register('GET', $path, $handler);
    }
    
    public function post($path, $handler) {
        return $this->register('POST', $path, $handler);
    }
    
    public function put($path, $handler) {
        return $this->register('PUT', $path, $handler);
    }
    
    public function delete($path, $handler) {
        return $this->register('DELETE', $path, $handler);
    }
    
    private function normalizeUri($uri) {
        // Remove .php extension if present
        $uri = preg_replace('/\.php$/', '', $uri);
        
        // Remove trailing slashes except for root
        $uri = $uri === '/' ? '/' : rtrim($uri, '/');
        
        // Ensure URI starts with /
        if (!empty($uri) && $uri[0] !== '/') {
            $uri = '/' . $uri;
        }
        
        return $uri;
    }
    
    public function dispatch($uri = null, $method = null) {
        $this->currentPath = $this->normalizeUri($uri ?? $this->currentPath);
        $this->currentMethod = $method ?? $this->currentMethod;

        if (defined('APP_DEBUG') && APP_DEBUG) {
            error_log("Router: Original URI: " . ($uri ?? $this->currentPath));
            error_log("Router: Normalized path: {$this->currentPath}");
            error_log("Router: Dispatching {$this->currentMethod} {$this->currentPath}");
            error_log("Router: Available routes for {$this->currentMethod}: " . implode(', ', array_keys($this->routes[$this->currentMethod] ?? [])));
        }

        if (!isset($this->routes[$this->currentMethod])) {
            error_log("Router: No routes defined for method {$this->currentMethod}");
            return $this->notFound();
        }
        
        foreach ($this->routes[$this->currentMethod] as $path => $handler) {
            $params = [];
            if (defined('APP_DEBUG') && APP_DEBUG) {
                error_log("Router: Attempting to match '{$this->currentPath}' against route '{$path}'");
            }
            if ($this->matchRoute($path, $params)) {
                if (defined('APP_DEBUG') && APP_DEBUG) {
                    error_log("Router: Match found! Calling handler {$handler}");
                    if ($params) {
                        error_log("Router: With parameters: " . json_encode($params));
                    }
                }
                return $this->callHandler($handler, $params);
            }
        }
        
        error_log("Router: No matching route found for {$this->currentMethod} {$this->currentPath}");
        return $this->notFound();
    }
    
    private function matchRoute($pattern, &$params) {
        // Convert route pattern to regex
        $pattern = preg_replace('/\//', '\\/', $pattern);
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^\/]+)', $pattern);
        $pattern = '/^' . $pattern . '$/i';
        
        if (defined('APP_DEBUG') && APP_DEBUG) {
            error_log("Router: Trying to match path: {$this->currentPath} against pattern: {$pattern}");
        }
        
        if (preg_match($pattern, $this->currentPath, $matches)) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                error_log("Router: Match found with matches: " . json_encode($matches));
            }
            // Get named parameters
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            if (defined('APP_DEBUG') && APP_DEBUG) {
                error_log("Router: Route matched with parameters: " . json_encode($params));
            }
            return true;
        }
        
        if (defined('APP_DEBUG') && APP_DEBUG) {
            error_log("Router: Route pattern did not match");
        }
        return false;
    }
    
    private function callHandler($handler, $params = []) {
        if (!str_contains($handler, '@')) {
            error_log("Router: Invalid handler format. Expected 'Controller@method', got '{$handler}'");
            return $this->notFound();
        }

        [$controller, $method] = explode('@', $handler);
        $controllerClass = $controller;
        $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';

        if (defined('APP_DEBUG') && APP_DEBUG) {
            error_log("Router: Looking for controller file: {$controllerFile}");
        }

        if (!file_exists($controllerFile)) {
            error_log("Router: Controller file not found: {$controllerFile}");
            return $this->notFound();
        }

        require_once $controllerFile;

        if (!class_exists($controllerClass)) {
            error_log("Router: Controller class '{$controllerClass}' not found in {$controllerFile}");
            return $this->notFound();
        }

        $controllerInstance = new $controllerClass();
        
        if (!method_exists($controllerInstance, $method)) {
            // Try namespaced version as fallback
            $namespacedClass = "App\\Controllers\\{$controllerClass}";
            if (class_exists($namespacedClass)) {
                $controllerInstance = new $namespacedClass();
                if (!method_exists($controllerInstance, $method)) {
                    error_log("Router: Method '{$method}' not found in controller '{$namespacedClass}'");
                    return $this->notFound();
                }
                $controllerClass = $namespacedClass;
            } else {
                error_log("Router: Method '{$method}' not found in controller '{$controllerClass}'");
                return $this->notFound();
            }
        }

        if (defined('APP_DEBUG') && APP_DEBUG) {
            error_log("Router: Calling {$controllerClass}::{$method} with parameters: " . json_encode($params));
        }

        try {
            // Convert associative array to positional array
            $positionalParams = array_values($params);
            return call_user_func_array([$controllerInstance, $method], $positionalParams);
        } catch (Exception $e) {
            error_log("Router: Error calling {$controllerClass}::{$method}: " . $e->getMessage());
            error_log("Router: " . $e->getTraceAsString());
            throw $e;
        }
    }
    
    private function notFound() {
        if (defined('APP_DEBUG') && APP_DEBUG) {
            error_log("Router: Returning 404 Not Found response");
            error_log("Router: Request was {$this->currentMethod} {$this->currentPath}");
        }
        
        http_response_code(404);
        
        // Try to load the 404 view with layout
        $viewPath = $this->viewsPath . '/errors/404.php';
        $headerPath = $this->layoutsPath . '/header.php';
        $footerPath = $this->layoutsPath . '/footer.php';
        
        // Set page title for the layout
        $pageTitle = "404 - Page Not Found";
        
        if (file_exists($headerPath)) {
            include $headerPath;
        }
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<div class='container mt-5'><h1>404 - Page Not Found</h1><p>The requested page could not be found.</p></div>";
        }
        
        if (file_exists($footerPath)) {
            include $footerPath;
        }
    }
}
