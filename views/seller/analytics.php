<?php
$pageTitle = APP_NAME . ' - Analytics';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
?>

<div class="container mt-4">
    <h1>Analytics</h1>

    <div class="row">
        <!-- Revenue Stats -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Revenue</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h6>Today</h6>
                            <h3>$<?php echo number_format($stats['revenue_stats']['today'], 2); ?></h3>
                        </div>
                        <div class="col-6">
                            <h6>This Month</h6>
                            <h3>$<?php echo number_format($stats['revenue_stats']['month'], 2); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Stats -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Orders</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($stats['order_stats'] as $status => $count): ?>
                        <div class="col-4">
                            <h6><?php echo ucfirst($status); ?></h6>
                            <h3><?php echo $count; ?></h3>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Stats -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Products</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h6>Total Active</h6>
                            <h3><?php echo $stats['total_products']; ?></h3>
                        </div>
                        <div class="col-6">
                            <h6>Low Stock</h6>
                            <h3><?php echo $stats['low_stock_count']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>