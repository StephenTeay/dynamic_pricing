<?php
// public/seller/dashboard.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Order.php';

AuthController::requireSeller();

$userId = Session::getUserId();

// Get seller ID
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT seller_id, business_name FROM seller_profiles WHERE user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
$seller = $stmt->fetch();
$sellerId = $seller['seller_id'];

// Get dashboard stats
$productModel = new Product();
$orderModel = new Order();

$totalProducts = $productModel->count(['seller_id' => $sellerId, 'is_active' => 1]);
$lowStockProducts = $productModel->getLowStockProducts($sellerId);
$lowStockCount = count($lowStockProducts);

// Get revenue stats
$revenueStats = $orderModel->getRevenueStats($sellerId);
$totalRevenue = $revenueStats['total_revenue'] ?? 0;
$totalOrders = $revenueStats['total_orders'] ?? 0;

// Get recent orders
$recentOrders = $orderModel->getOrdersBySeller($sellerId);
$recentOrders = array_slice($recentOrders, 0, 5);

// Get price change history
$priceHistoryQuery = "SELECT ph.*, p.product_name 
                      FROM pricing_history ph
                      INNER JOIN products p ON ph.product_id = p.product_id
                      WHERE p.seller_id = :seller_id
                      ORDER BY ph.changed_at DESC
                      LIMIT 10";
$stmt = $db->prepare($priceHistoryQuery);
$stmt->execute([':seller_id' => $sellerId]);
$priceHistory = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/seller.css">
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/seller_nav.php'; ?>
    
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($seller['business_name']); ?>!</h1>
        
        <?php if ($flash = Session::getFlash('success')): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($flash); ?></div>
        <?php endif; ?>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p class="stat-value"><?php echo $totalProducts; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p class="stat-value">₦<?php echo number_format($totalRevenue, 2); ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p class="stat-value"><?php echo $totalOrders; ?></p>
            </div>
            
            <div class="stat-card alert">
                <h3>Low Stock Items</h3>
                <p class="stat-value"><?php echo $lowStockCount; ?></p>
            </div>
        </div>
        
        <!-- Low Stock Alerts -->
        <?php if ($lowStockCount > 0): ?>
        <div class="section">
            <h2>Low Stock Alerts</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Current Stock</th>
                            <th>Threshold</th>
                            <th>Current Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockProducts as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td class="text-danger"><?php echo $product['quantity_available']; ?></td>
                            <td><?php echo $product['low_stock_threshold']; ?></td>
                            <td>₦<?php echo number_format($product['current_price'], 2); ?></td>
                            <td>
                                <a href="<?php echo url('/seller/inventory') . '?product_id=' . urlencode($product['product_id']); ?>" class="btn btn-sm">
                                    Restock
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Recent Price Changes -->
        <div class="section">
            <h2>Recent Price Changes</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Old Price</th>
                            <th>New Price</th>
                            <th>Change</th>
                            <th>Reason</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($priceHistory as $history): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($history['product_name']); ?></td>
                            <td>₦<?php echo number_format($history['old_price'], 2); ?></td>
                            <td>₦<?php echo number_format($history['new_price'], 2); ?></td>
                            <td class="<?php echo $history['price_change_percent'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?php echo $history['price_change_percent'] >= 0 ? '+' : ''; ?>
                                <?php echo number_format($history['price_change_percent'], 2); ?>%
                            </td>
                            <td><?php echo htmlspecialchars($history['change_reason']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($history['changed_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="section">
            <h2>Recent Orders</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                            <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                            <td>₦<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $order['order_status']; ?>">
                                    <?php echo ucfirst($order['order_status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="<?php echo url('/seller/order_detail') . '?id=' . urlencode($order['order_id']); ?>" class="btn btn-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/seller/dashboard.js"></script>
</body>
</html>