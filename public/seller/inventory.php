<?php
// public/seller/inventory.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Inventory.php';
require_once __DIR__ . '/../../core/Validator.php';

AuthController::requireSeller();

$userId = Session::getUserId();

// Get seller ID
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT seller_id FROM seller_profiles WHERE user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
$seller = $stmt->fetch();
$sellerId = $seller['seller_id'];

$productModel = new Product();
$inventoryModel = new Inventory();

// Get specific product if ID provided
$productId = $_GET['product_id'] ?? null;
$product = null;
$inventory = null;

if ($productId) {
    $product = $productModel->find($productId);
    if (!$product || $product['seller_id'] != $sellerId) {
        Session::setFlash('error', 'Product not found.');
        header('Location: /seller/inventory.php');
        exit;
    }
    $inventory = $inventoryModel->getByProductId($productId);
}

// Handle inventory update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $productId = (int)$_POST['product_id'];
    
    // Verify product belongs to seller
    $product = $productModel->find($productId);
    if (!$product || $product['seller_id'] != $sellerId) {
        Session::setFlash('error', 'Unauthorized access.');
        header('Location: /seller/inventory.php');
        exit;
    }
    
    if ($action === 'update') {
        $quantity = (int)$_POST['quantity'];
        $lowThreshold = (int)$_POST['low_stock_threshold'];
        $highThreshold = (int)$_POST['high_stock_threshold'];
        
        if ($inventoryModel->updateStock($productId, $quantity)) {
            $stmt = $db->prepare(
                "UPDATE inventory SET low_stock_threshold = :low, 
                 high_stock_threshold = :high WHERE product_id = :product_id"
            );
            $stmt->execute([
                ':low' => $lowThreshold,
                ':high' => $highThreshold,
                ':product_id' => $productId
            ]);
            
            Session::setFlash('success', 'Inventory updated successfully!');
        } else {
            Session::setFlash('error', 'Failed to update inventory.');
        }
        
        header('Location: /seller/inventory.php?product_id=' . $productId);
        exit;
    } elseif ($action === 'adjust') {
        $adjustment = (int)$_POST['adjustment'];
        $inventoryModel->adjustStock($productId, $adjustment);
        Session::setFlash('success', 'Stock adjusted!');
        header('Location: /seller/inventory.php?product_id=' . $productId);
        exit;
    }
}

// Get all products with inventory
$products = $productModel->getProductsWithInventory($sellerId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/seller.css">
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/seller_nav.php'; ?>
    
    <div class="container">
        <h1>Inventory Management</h1>
        
        <?php if ($success = Session::getFlash('success')): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error = Session::getFlash('error')): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($product && $inventory): ?>
            <!-- Single Product Inventory Edit -->
            <div class="form-section">
                <h2><?php echo htmlspecialchars($product['product_name']); ?> - Inventory</h2>
                
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="quantity">Current Stock *</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" 
                                   value="<?php echo $inventory['quantity_available']; ?>" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="quantity_reserved">Reserved Stock</label>
                            <input type="number" class="form-control" 
                                   value="<?php echo $inventory['quantity_reserved']; ?>" disabled>
                            <small>Units reserved for pending orders</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="low_stock_threshold">Low Stock Threshold *</label>
                            <input type="number" id="low_stock_threshold" name="low_stock_threshold" 
                                   class="form-control" value="<?php echo $inventory['low_stock_threshold']; ?>" 
                                   min="0" required>
                            <small>Trigger price increase when stock falls below this</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="high_stock_threshold">High Stock Threshold *</label>
                            <input type="number" id="high_stock_threshold" name="high_stock_threshold" 
                                   class="form-control" value="<?php echo $inventory['high_stock_threshold']; ?>" 
                                   min="0" required>
                            <small>Trigger price decrease when stock exceeds this</small>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <a href="/seller/inventory.php" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Update Inventory</button>
                    </div>
                </form>
                
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e2e8f0;">
                    <h3>Quick Stock Adjustment</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="adjust">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        
                        <div class="form-group">
                            <label for="adjustment">Adjustment (positive or negative) *</label>
                            <input type="number" id="adjustment" name="adjustment" class="form-control" 
                                   placeholder="e.g., +10 or -5" required>
                        </div>
                        
                        <button type="submit" class="btn btn-secondary">Apply Adjustment</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Inventory Overview -->
            <div class="section">
                <h2>All Products Inventory</h2>
                
                <?php if (empty($products)): ?>
                    <p style="text-align: center; padding: 2rem; color: #64748b;">
                        No products found. <a href="/seller/products.php">Add products first</a>
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Available</th>
                                    <th>Reserved</th>
                                    <th>Threshold</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $prod): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($prod['product_name']); ?></strong>
                                        <br>
                                        <small style="color: #64748b;">SKU: <?php echo htmlspecialchars($prod['sku']); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo $prod['quantity_available']; ?></strong>
                                    </td>
                                    <td>
                                        <?php echo $prod['quantity_reserved']; ?>
                                    </td>
                                    <td>
                                        Low: <?php echo $prod['low_stock_threshold']; ?>
                                        <br>
                                        High: <?php echo $prod['high_stock_threshold']; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $qty = $prod['quantity_available'];
                                        $low = $prod['low_stock_threshold'];
                                        $high = $prod['high_stock_threshold'];
                                        
                                        if ($qty == 0) {
                                            echo '<span class="badge badge-danger">Out of Stock</span>';
                                        } elseif ($qty <= $low) {
                                            echo '<span class="badge badge-warning">Low Stock</span>';
                                        } elseif ($qty >= $high) {
                                            echo '<span class="badge badge-warning">Overstock</span>';
                                        } else {
                                            echo '<span class="badge badge-success">Optimal</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="/seller/inventory.php?product_id=<?php echo $prod['product_id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
</body>
</html>