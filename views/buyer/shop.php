<?php
// views/buyer/shop.php
$productModel = new Product();
$category = $_GET['category'] ?? null;
$search = $_GET['search'] ?? null;
$minPrice = $_GET['min_price'] ?? null;
$maxPrice = $_GET['max_price'] ?? null;
$page = (int)($_GET['page'] ?? 1);
$limit = PRODUCTS_PER_PAGE;
$offset = ($page - 1) * $limit;

if ($search) {
    $products = $productModel->searchProducts($search, $category);
} elseif ($minPrice && $maxPrice) {
    $products = $productModel->getProductsByPriceRange($minPrice, $maxPrice, $category);
} elseif ($category) {
    $products = $productModel->getProductsByCategory($category, $limit);
} else {
    $products = $productModel->findAll(['is_active' => 1], 'created_at DESC', $limit, $offset);
}

$categories = ['Electronics', 'Fashion', 'Home', 'Books', 'Sports'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo rtrim(BASE_URL, '/'); ?>">
    <title>Shop - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/buyer.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/toast.css">
    <script>
        window.BASE_URL = '<?php echo BASE_URL; ?>';
                    quantity = parseInt(quantity);
                    
                    const existingItem = this.items.find(item => item.id === productId);
                    if (existingItem) {
                        existingItem.quantity += quantity;
                    } else {
                        this.items.push({ id: productId, quantity: quantity });
                    }
                    this.save();
                    this.showToast('Added to cart successfully!', 'success');
                

                save: function() {
                    localStorage.setItem('cart', JSON.stringify(this.items));
                    this.updateCartCount();
                },

                updateCartCount: function() {
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        const count = this.items.reduce((total, item) => total + item.quantity, 0);
                        cartCount.textContent = count;
                        cartCount.style.display = count > 0 ? 'block' : 'none';
                    }
                },

                showToast: function(message, type = 'success') {
                    const toast = document.createElement('div');
                    toast.className = `toast ${type}`;
                    toast.textContent = message;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                }
            

            // Initialize cart
            cart.init();

            // Add global addToCart function
            window.addToCart = function(productId) {
                try {
                    const quantity = document.getElementById('quantity')?.value || 1;
                    cart.addItem(productId, quantity);
                } catch (error) {
                    console.error('Error adding to cart:', error);
                    cart.showToast('Failed to add item to cart. Please try again.', 'error');
                }
            };
       
    </script>
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/buyer_nav.php'; ?>
    
    <div class="container">
        <h1>Shop</h1>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form action="<?php echo url('buyer/shop'); ?>" method="GET" class="filters">
                <div class="form-group">
                    <label for="category">Category</label>
                    <select name="category" id="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Search products...">
                </div>

                <div class="form-group">
                    <label>Price Range</label>
                    <div class="price-range">
                        <input type="number" name="min_price" placeholder="Min" value="<?php echo htmlspecialchars($minPrice ?? ''); ?>" min="0">
                        <input type="number" name="max_price" placeholder="Max" value="<?php echo htmlspecialchars($maxPrice ?? ''); ?>" min="0">
                    </div>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="apply">Filter</button>
                    <a href="<?php echo url('buyer/shop'); ?>" class="reset">Reset</a>
                </div>
            </form>
        </div>
        
        <!-- Products Grid -->
        <?php if (empty($products)): ?>
            <div class="no-products">
                <p>No products found. Try different filters.</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <?php include __DIR__ . '/../../views/components/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include __DIR__ . '/../../views/layouts/footer.php'; ?>

    <script>
        window.appConfig = {
            basePath: '<?php echo BASE_PATH; ?>'
        };
    </script>
    <script src="<?php echo asset('js/app.js'); ?>"></script>
    <script src="<?php echo asset('js/buyer/cart.js'); ?>"></script>
</body>
</html>