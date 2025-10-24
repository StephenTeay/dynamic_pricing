<?php
// views/buyer/orders.php
if (!defined('BASE_PATH')) {
    exit('No direct script access allowed');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo BASE_URL; ?>">
    <title>My Orders - Dynamic Pricing</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/buyer.css">
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/buyer_nav.php'; ?>
    
    <div class="container">
        <h1>My Orders</h1>
        
        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <p>You haven't placed any orders yet.</p>
                <a href="<?php echo BASE_URL; ?>/buyer/shop" class="btn">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo htmlspecialchars($order['id']); ?></h3>
                            <span class="order-date">
                                <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                            </span>
                            <span class="order-status <?php echo strtolower($order['status']); ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </div>
                        
                        <div class="order-items">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="order-item">
                                    <img src="<?php echo ASSETS_URL; ?>/images/products/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                         class="item-image">
                                    <div class="item-details">
                                        <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                        <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                        <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-footer">
                            <div class="order-total">
                                Total: $<?php echo number_format($order['total_amount'], 2); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="<?php echo ASSETS_URL; ?>/js/api.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
</body>
</html>