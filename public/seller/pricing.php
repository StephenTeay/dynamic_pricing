<?php
// public/seller/pricing.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/PricingRule.php';

AuthController::requireSeller();

$userId = Session::getUserId();

// Get seller ID
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT seller_id FROM seller_profiles WHERE user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
$seller = $stmt->fetch();
$sellerId = $seller['seller_id'];

$productModel = new Product();
$products = $productModel->getProductsWithInventory($sellerId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Management</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/seller.css">
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/seller_nav.php'; ?>
    
    <div class="container">
        <h1>Pricing Management</h1>
        
        <div class="section">
            <h2>Manage Product Prices</h2>
            
            <?php if (empty($products)): ?>
                <p style="text-align: center; padding: 2rem;">No products available for pricing management.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Base Cost</th>
                                <th>Current Price</th>
                                <th>Margin</th>
                                <th>Stock Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                                </td>
                                <td>₦<?php echo number_format($product['base_cost'], 2); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                        <input type="number" name="new_price" step="0.01" 
                                               value="<?php echo $product['current_price']; ?>" 
                                               style="width: 120px; padding: 0.25rem;">
                                    </form>
                                </td>
                                <td>
                                    <?php 
                                    $margin = (($product['current_price'] - $product['base_cost']) / $product['base_cost']) * 100;
                                    echo number_format($margin, 2) . '%';
                                    ?>
                                </td>
                                <td>
                                    <span class="<?php echo $product['quantity_available'] <= $product['low_stock_threshold'] ? 'product-item-stock-low' : ''; ?>">
                                        <?php echo $product['quantity_available']; ?> / <?php echo $product['high_stock_threshold']; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="updatePrice(<?php echo $product['product_id']; ?>)">Update</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include __DIR__ . '/../../views/layouts/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/seller/pricing.js"></script>
</body>
</html>
