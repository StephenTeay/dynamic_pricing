<?php
// core/Controller.php

class Controller {
    protected $db;
    
    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Render a view
     */
    protected function render($view, $data = []) {
        extract($data);
        $file = __DIR__ . '/../views/' . $view . '.php';
        
        if (!file_exists($file)) {
            throw new Exception("View not found: $view");
        }
        
        include $file;
    }
    
    /**
     * Return JSON response
     */
    protected function jsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    /**
     * Set flash message
     */
    protected function setFlash($type, $message) {
        Session::setFlash($type, $message);
    }
}
?>
