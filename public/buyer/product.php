<?php
// public/buyer/product.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../models/Product.php';

Session::start();

$productId = $_GET['id'] ?? 0;

if (!$productId) {
    header('Location: /buyer/shop.php');
    exit;
}

$productModel = new Product();
$product = $productModel->getProductDetail($productId);

if (!$product) {
    header('Location: /buyer/shop.php');
    exit;
}

// Get pricing history
$db = (new Database())->getConnection();
$historyStmt = $db->prepare(
    "SELECT * FROM pricing_history WHERE product_id = :product_id 
     ORDER BY changed_at DESC LIMIT 10"
);
$historyStmt->execute([':product_id' => $productId]);
$priceHistory = $historyStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - Shop</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/buyer.css">
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/buyer_nav.php'; ?>
    
    <div class="container">
        <div class="product-detail-container">
            <div>
                <div class="product-detail-image">
                    <?php if ($product['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    <?php else: ?>
                        <img src="<?php echo ASSETS_URL; ?>/images/no-image.png" alt="No image">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="product-detail-info">
                <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                
                <p style="color: #64748b; margin-bottom: 1rem;">
                    <strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?>
                </p>
                
                <p style="color: #64748b; margin-bottom: 1rem;">
                    <strong>Seller:</strong> <?php echo htmlspecialchars($product['business_name']); ?>
                </p>
                
                <div class="product-detail-price">
                    ₦<?php echo number_format($product['current_price'], 2); ?>
                </div>
                
                <p class="product-detail-description">
                    <?php echo nl2br(htmlspecialchars($product['product_description'])); ?>
                </p>
                
                <div style="margin-bottom: 1.5rem;">
                    <?php if ($product['quantity_available'] > 0): ?>
                        <p style="color: #10b981; font-weight: bold;">✓ In Stock</p>
                    <?php else: ?>
                        <p style="color: #ef4444; font-weight: bold;">✗ Out of Stock</p>
                    <?php endif; ?>
                    
                    <?php if ($product['quantity_available'] <= $product['low_stock_threshold'] && $product['quantity_available'] > 0): ?>
                        <p style="color: #f59e0b; font-size: 0.875rem;">
                            Only <?php echo $product['quantity_available']; ?> units available
                        </p>
                    <?php endif; ?>
                </div>
                
                <div class="quantity-selector">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" class="quantity-input" 
                           value="1" min="1" max="<?php echo $product['quantity_available']; ?>"
                           <?php echo $product['quantity_available'] <= 0 ? 'disabled' : ''; ?>>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <?php if ($product['quantity_available'] > 0): ?>
                        <button onclick="addToCart(<?php echo $product['product_id']; ?>)" 
                                class="btn btn-primary" style="flex: 1;">
                            Add to Cart
                        </button>
                        <button onclick="addToWishlist(<?php echo $product['product_id']; ?>)" 
                                class="btn btn-secondary">
                            ♥ Wishlist
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled style="flex: 1;">
                            Out of Stock
                        </button>
                    <?php endif; ?>
                </div>
                
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e2e8f0;">
                    <h3 style="margin-bottom: 1rem;">Product Details</h3>
                    <table style="width: 100%;">
                        <tr>
                            <td style="padding: 0.5rem 0; color: #64748b;"><strong>SKU:</strong></td>
                            <td style="padding: 0.5rem 0;"><?php echo htmlspecialchars($product['sku']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 0.5rem 0; color: #64748b;"><strong>Availability:</strong></td>
                            <td style="padding: 0.5rem 0;">
                                <?php 
                                if ($product['quantity_available'] > $product['low_stock_threshold']) {
                                    echo '<span class="badge" style="background-color: #d1fae5; color: #065f46;">In Stock</span>';
                                } elseif ($product['quantity_available'] > 0) {
                                    echo '<span class="badge" style="background-color: #fef3c7; color: #92400e;">Limited Stock</span>';
                                } else {
                                    echo '<span class="badge" style="background-color: #fee2e2; color: #991b1b;">Out of Stock</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Price History -->
        <?php if (!empty($priceHistory)): ?>
        <div class="section" style="margin-top: 2rem;">
            <h2>Price History (Last 10 Changes)</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Old Price</th>
                            <th>New Price</th>
                            <th>Change</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($priceHistory as $history): ?>
                        <tr>
                            <td><?php echo date('M d, Y H:i', strtotime($history['changed_at'])); ?></td>
                            <td>₦<?php echo number_format($history['old_price'], 2); ?></td>
                            <td>₦<?php echo number_format($history['new_price'], 2); ?></td>
                            <td class="<?php echo $history['price_change_percent'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?php echo $history['price_change_percent'] >= 0 ? '+' : ''; ?>
                                <?php echo number_format($history['price_change_percent'], 2); ?>%
                            </td>
                            <td><?php echo htmlspecialchars($history['change_reason']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/buyer/shop.js"></script>
    <script>
        function addToCart(productId) {
            const quantity = parseInt(document.getElementById('quantity').value);
            Cart.addItem(productId, quantity);
        }
        
        function addToWishlist(productId) {
            // Placeholder for wishlist functionality
            Toast.show('Added to wishlist!', 'success');
        }
    </script>
</body>
</html>