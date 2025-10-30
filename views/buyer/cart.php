<?php
// views/buyer/cart.php
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
    <title>Shopping Cart - Dynamic Pricing</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/buyer.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/toast.css">
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/buyer_nav.php'; ?>
    
    <div class="container">
        <h1>Shopping Cart</h1>
        
        <div id="cart-container">
            <!-- Cart items will be loaded dynamically via JavaScript -->
            <div class="loading">Loading cart items...</div>
        </div>
    </div>

    <script>
        // Define the base URL for use in scripts
        window.BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/buyer/cart.js"></script>
    <script>
        // Initialize cart page functionality when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Render cart items
                renderCart();
            } catch (error) {
                console.error('Error rendering cart:', error);
                document.getElementById('cart-container').innerHTML = `
                    <div class="cart-error">
                        <p>Failed to load cart items. Please try again.</p>
                        <button onclick="window.location.reload()" class="btn">Retry</button>
                        <a href="${BASE_URL}/buyer/shop" class="btn">Continue Shopping</a>
                    </div>`;
            }
        });
    </script>
</body>
</html>