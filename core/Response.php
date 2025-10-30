<?php
// core/Response.php

class Response {
    public static function json($data, $statusCode = 200) {
        // Disable error reporting for this response
        error_reporting(0);
        ini_set('display_errors', 0);
        
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set headers
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        
        // Convert data to JSON
        $json = json_encode($data);
        
        // Check for JSON encoding errors
        if ($json === false) {
            // Log the error
            error_log("JSON encoding error: " . json_last_error_msg());
            
            // Send a generic error response
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Internal server error'
            ]);
            exit;
        }
        
        // Output the JSON
        echo $json;
        exit;
}
}
?>
