<?php
// api/v1/pricing.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/PricingRule.php';
require_once __DIR__ . '/../../models/PricingHistory.php';
require_once __DIR__ . '/../../models/Product.php';
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
$productId = $parts[4] ?? null;

$pricingRuleModel = new PricingRule();
$productModel = new Product();

try {
    if ($method === 'GET') {
        if (!Session::isLoggedIn() || Session::getUserType() !== 'seller') {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        $sellerId = Session::getUserId();
        
        if ($action === 'rules' && $productId) {
            $rules = $pricingRuleModel->getByProductId($productId);
            jsonResponse(['data' => $rules]);
            
        } elseif ($action === 'history' && $productId) {
            $historyModel = new PricingHistory();
            $history = $historyModel->getByProductId($productId);
            jsonResponse(['data' => $history]);
        } else {
            jsonResponse(['error' => 'Invalid action'], 400);
        }
        
    } elseif ($method === 'POST') {
        if (!Session::isLoggedIn() || Session::getUserType() !== 'seller') {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        if ($action === 'create-rule') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['product_id'], $input['rule_type'])) {
                jsonResponse(['error' => 'Missing required fields'], 400);
            }
            
            $product = $productModel->find($input['product_id']);
            if (!$product || $product['seller_id'] != Session::getUserId()) {
                jsonResponse(['error' => 'Unauthorized'], 401);
            }
            
            $ruleId = $pricingRuleModel->create($input);
            
            jsonResponse([
                'success' => true,
                'message' => 'Pricing rule created',
                'rule_id' => $ruleId
            ], 201);
            
        } else {
            jsonResponse(['error' => 'Invalid action'], 400);
        }
        
    } elseif ($method === 'PUT') {
        if (!Session::isLoggedIn() || Session::getUserType() !== 'seller') {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        if ($action === 'update-rule' && $productId) {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if ($pricingRuleModel->update($productId, $input)) {
                jsonResponse(['success' => true, 'message' => 'Rule updated']);
            } else {
                jsonResponse(['error' => 'Failed to update rule'], 500);
            }
        }
        
    } elseif ($method === 'DELETE') {
        if (!Session::isLoggedIn() || Session::getUserType() !== 'seller') {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        if ($action === 'delete-rule' && $productId) {
            if ($pricingRuleModel->delete($productId)) {
                jsonResponse(['success' => true, 'message' => 'Rule deleted']);
            } else {
                jsonResponse(['error' => 'Failed to delete rule'], 500);
            }
        }
        
    } else {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
    
} catch (Exception $e) {
    Logger::exception($e);
    jsonResponse(['error' => 'Server error'], 500);
}
?>
