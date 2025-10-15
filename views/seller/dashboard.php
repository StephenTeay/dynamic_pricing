<?php
// views/seller/dashboard.php
$pageTitle = APP_NAME . ' - Seller Dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
?>

<div class="container mt-4">
    <h1>Seller Dashboard</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Products</h5>
                    <p class="card-text">Manage your product catalog</p>
                    <a href="<?php echo url('seller/products'); ?>" class="btn btn-primary">View Products</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Orders</h5>
                    <p class="card-text">View and manage orders</p>
                    <a href="<?php echo url('seller/orders'); ?>" class="btn btn-primary">View Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Analytics</h5>
                    <p class="card-text">View sales and performance metrics</p>
                    <a href="<?php echo url('seller/analytics'); ?>" class="btn btn-primary">View Analytics</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Inventory</h5>
                    <p class="card-text">Manage your inventory levels</p>
                    <a href="<?php echo url('seller/inventory'); ?>" class="btn btn-primary">Manage Inventory</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Pricing</h5>
                    <p class="card-text">Set and update product prices</p>
                    <a href="<?php echo url('seller/pricing'); ?>" class="btn btn-primary">Manage Pricing</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Settings</h5>
                    <p class="card-text">Update your seller profile and preferences</p>
                    <a href="<?php echo url('seller/settings'); ?>" class="btn btn-primary">Settings</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>