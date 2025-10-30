<?php
// public/seller/analytics.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Analytics.php';

AuthController::requireSeller();

$userId = Session::getUserId();

// Get seller ID
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT seller_id FROM seller_profiles WHERE user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
$seller = $stmt->fetch();
$sellerId = $seller['seller_id'];

$orderModel = new Order();
$analyticsModel = new Analytics();

$period = $_GET['period'] ?? 'month';
$revenueStats = $orderModel->getRevenueStats($sellerId, $period);
$salesData = $analyticsModel->getSalesAnalytics($sellerId, 30);
$productAnalytics = $analyticsModel->getProductAnalytics($sellerId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Seller Dashboard</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/seller.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/seller_nav.php'; ?>
    
    <div class="container">
        <h1>Analytics</h1>
        
        <!-- Revenue Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p class="stat-value">₦<?php echo number_format($revenueStats['total_revenue'] ?? 0, 2); ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p class="stat-value"><?php echo $revenueStats['total_orders'] ?? 0; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Average Order Value</h3>
                <p class="stat-value">₦<?php echo number_format($revenueStats['avg_order_value'] ?? 0, 2); ?></p>
            </div>
        </div>
        
        <!-- Sales Chart -->
        <div class="chart-container">
            <h2>Sales Trend (Last 30 Days)</h2>
            <canvas id="salesChart"></canvas>
        </div>
        
        <!-- Top Products -->
        <div class="section">
            <h2>Top Performing Products</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Sales</th>
                            <th>Revenue</th>
                            <th>Avg Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $topProducts = array_slice($productAnalytics, 0, 5);
                        foreach ($topProducts as $product): 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo $product['sales'] ?? 0; ?></td>
                            <td>₦<?php echo number_format($product['revenue'] ?? 0, 2); ?></td>
                            <td>₦<?php echo number_format($product['avg_price'] ?? 0, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../../views/layouts/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/seller/analytics.js"></script>
    <script>
        // Sales chart
        const salesData = <?php echo json_encode($salesData ?? []); ?>;
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.map(d => d.date),
                datasets: [{
                    label: 'Revenue',
                    data: salesData.map(d => d.total_revenue),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    </script>
</body>
</html>
