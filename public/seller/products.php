<?php
// public/seller/products.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/Product.php';

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
    <title>My Products - Seller Dashboard</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/seller.css">
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/seller_nav.php'; ?>
    
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>My Products</h1>
            <a href="<?php echo url('/seller/product/create'); ?>" class="btn btn-primary">+ Add Product</a>
        </div>
        
        <?php if (empty($products)): ?>
            <div class="section">
                <p style="text-align: center; padding: 2rem;">
                    No products yet. <a href="<?php echo url('/seller/product/create'); ?>">Create your first product</a>
                </p>
            </div>
        <?php else: ?>
            <div class="section">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                                    <br>
                                    <small><?php echo htmlspecialchars($product['sku']); ?></small>
                                </td>
                                <td>₦<?php echo number_format($product['current_price'], 2); ?></td>
                                <td><?php echo $product['quantity_available']; ?></td>
                                <td>
                                    <?php echo $product['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?>
                                </td>
                                <td>
                                    <a href="<?php echo url('/seller/product/edit/' . $product['product_id']); ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="<?php echo url('/seller/inventory') . '?product_id=' . urlencode($product['product_id']); ?>" class="btn btn-sm btn-secondary">Stock</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include __DIR__ . '/../../views/layouts/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/seller/products.js"></script>
</body>
</html>
