<?php
// api/v1/auth.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../utils/logger.php';

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

Session::start();

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));
$action = $parts[3] ?? null;

$userModel = new User();

try {
    if ($method === 'POST') {
        if ($action === 'login') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['identifier'], $input['password'])) {
                jsonResponse(['error' => 'Identifier and password required'], 400);
            }
            
            $user = $userModel->verifyLogin($input['identifier'], $input['password']);
            
            if (!$user) {
                jsonResponse(['error' => 'Invalid credentials'], 401);
            }
            
            Session::login($user['user_id'], $user['user_type'], $user['username']);
            
            jsonResponse([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'user_type' => $user['user_type']
                ]
            ]);
            
        } elseif ($action === 'register') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['username'], $input['email'], $input['password'], $input['user_type'])) {
                jsonResponse(['error' => 'Missing required fields'], 400);
            }
            
            if ($userModel->emailExists($input['email'])) {
                jsonResponse(['error' => 'Email already registered'], 400);
            }
            
            if ($userModel->usernameExists($input['username'])) {
                jsonResponse(['error' => 'Username already taken'], 400);
            }
            
            $userId = $userModel->createUser($input);
            
            jsonResponse([
                'success' => true,
                'message' => 'Registration successful',
                'user_id' => $userId
            ], 201);
        } else {
            jsonResponse(['error' => 'Invalid action'], 400);
        }
        
    } elseif ($method === 'GET') {
        if ($action === 'check') {
            if (Session::isLoggedIn()) {
                jsonResponse([
                    'authenticated' => true,
                    'user_id' => Session::getUserId(),
                    'user_type' => Session::getUserType()
                ]);
            } else {
                jsonResponse(['authenticated' => false], 401);
            }
        } else {
            jsonResponse(['error' => 'Invalid action'], 400);
        }
        
    } else {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
    
} catch (Exception $e) {
    Logger::exception($e);
    jsonResponse(['error' => 'Server error'], 500);
}
?>
