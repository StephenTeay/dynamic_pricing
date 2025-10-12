<?php
// public/seller/orders.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/Order.php';

AuthController::requireSeller();

$userId = Session::getUserId();

// Get seller ID
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT seller_id FROM seller_profiles WHERE user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
$seller = $stmt->fetch();
$sellerId = $seller['seller_id'];

$orderModel = new Order();
$orders = $orderModel->getOrdersBySeller($sellerId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Seller Dashboard</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/seller.css">
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/seller_nav.php'; ?>
    
    <div class="container">
        <h1>Orders</h1>
        
        <?php if (empty($orders)): ?>
            <div class="section">
                <p style="text-align: center; padding: 2rem;">No orders yet.</p>
            </div>
        <?php else: ?>
            <div class="section">
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
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                                <td>₦<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $order['order_status']; ?>">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="/seller/order_detail.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary">View</a>
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
</body>
</html>
