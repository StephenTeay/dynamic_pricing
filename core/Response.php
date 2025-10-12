<?php
// core/Response.php

class Response {
    private $statusCode = 200;
    private $headers = [];
    private $body = '';
    
    public function __construct($body = '', $statusCode = 200) {
        $this->body = $body;
        $this->statusCode = $statusCode;
    }
    
    public function setStatusCode($code) {
        $this->statusCode = $code;
        return $this;
    }
    
    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }
    
    public function setBody($body) {
        $this->body = $body;
        return $this;
    }
    
    public function json($data) {
        $this->setHeader('Content-Type', 'application/json');
        $this->body = json_encode($data);
        return $this;
    }
    
    public function send() {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        
        echo $this->body;
    }
}
?>
