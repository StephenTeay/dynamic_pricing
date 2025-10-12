<?php
// views/home.php
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/layouts/buyer_nav.php';
?>

<div class="hero">
    <div class="container">
        <h1><?php echo APP_NAME; ?></h1>
        <p>Welcome to our dynamic pricing platform</p>
    </div>
</div>

<div class="container">
    <div class="featured-products">
        <!-- Featured products will go here -->
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>