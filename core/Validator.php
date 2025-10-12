<?php
// core/Validator.php

class Validator {
    private $data = [];
    private $errors = [];
    
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    public static function sanitize($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitize($value);
            } else {
                $sanitized[$key] = trim(stripslashes(htmlspecialchars($value)));
            }
        }
        return $sanitized;
    }
    
    public function validate($rules) {
        foreach ($rules as $field => $rule) {
            $ruleArray = explode('|', $rule);
            foreach ($ruleArray as $r) {
                $this->validateRule($field, $r);
            }
        }
        return count($this->errors) === 0;
    }
    
    private function validateRule($field, $rule) {
        $value = $this->data[$field] ?? '';
        
        if (strpos($rule, ':') !== false) {
            [$ruleName, $ruleValue] = explode(':', $rule);
        } else {
            $ruleName = $rule;
        }
        
        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->errors[$field] = ucfirst($field) . ' is required';
                }
                break;
                
            case 'min':
                if (!empty($value) && strlen($value) < $ruleValue) {
                    $this->errors[$field] = ucfirst($field) . ' must be at least ' . $ruleValue . ' characters';
                }
                break;
                
            case 'max':
                if (!empty($value) && strlen($value) > $ruleValue) {
                    $this->errors[$field] = ucfirst($field) . ' must not exceed ' . $ruleValue . ' characters';
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = ucfirst($field) . ' must be a valid email';
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field] = ucfirst($field) . ' must be numeric';
                }
                break;
                
            case 'alphanumeric':
                if (!empty($value) && !ctype_alnum($value)) {
                    $this->errors[$field] = ucfirst($field) . ' must be alphanumeric';
                }
                break;
                
            case 'confirmed':
                if ($value !== ($this->data[$ruleValue] ?? '')) {
                    $this->errors[$field] = ucfirst($field) . ' does not match';
                }
                break;
                
            case 'in':
                $values = explode(',', $ruleValue);
                if (!empty($value) && !in_array($value, $values)) {
                    $this->errors[$field] = ucfirst($field) . ' is invalid';
                }
                break;
                
            case 'phone':
                if (!empty($value) && !preg_match('/^[0-9+\-\s\(\)]{7,}$/', $value)) {
                    $this->errors[$field] = ucfirst($field) . ' is invalid';
                }
                break;
        }
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getFirstError() {
        return array_values($this->errors)[0] ?? null;
    }
    
    public function hasError($field) {
        return isset($this->errors[$field]);
    }
    
    public function getError($field) {
        return $this->errors[$field] ?? null;
    }
}
?>
