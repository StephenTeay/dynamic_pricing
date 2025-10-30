<?php
// public/buyer/shop.php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../models/Product.php';

Session::start();

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
    
    <!-- Initialize app configuration -->
    <script>
        window.appConfig = {
            baseUrl: '<?php echo rtrim(BASE_URL, '/'); ?>',
            assetsUrl: '<?php echo rtrim(ASSETS_URL, '/'); ?>'
        };
    </script>
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/buyer_nav.php'; ?>
    
    <div class="container">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <h1 class="page-title">Discover Amazing Products</h1>
            <p class="page-subtitle">Find exactly what you're looking for from our curated collection</p>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="search">Search</label>
                    <input type="text" 
                           id="search"
                           name="search" 
                           class="form-control" 
                           placeholder="Search products..." 
                           value="<?php echo htmlspecialchars($search ?? ''); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="category">Category</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="min_price">Min Price</label>
                    <input type="number" 
                           id="min_price"
                           name="min_price" 
                           class="form-control" 
                           placeholder="Min Price" 
                           value="<?php echo htmlspecialchars($minPrice ?? ''); ?>"
                           min="0"
                           step="0.01">
                </div>
                
                <div class="filter-group">
                    <label for="max_price">Max Price</label>
                    <input type="number" 
                           id="max_price"
                           name="max_price" 
                           class="form-control" 
                           placeholder="Max Price" 
                           value="<?php echo htmlspecialchars($maxPrice ?? ''); ?>"
                           min="0"
                           step="0.01">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <span>Apply Filters</span>
                </button>
                
                <?php if ($search || $category || $minPrice || $maxPrice): ?>
                    <a href="<?php echo BASE_URL; ?>/buyer/shop.php" class="btn btn-secondary">
                        <span>Clear Filters</span>
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Results Info -->
        <?php if (!empty($products)): ?>
            <div class="results-info mb-3">
                <p class="text-secondary">
                    <strong><?php echo count($products); ?></strong> product<?php echo count($products) !== 1 ? 's' : ''; ?> found
                    <?php if ($category): ?>
                        in <strong><?php echo htmlspecialchars($category); ?></strong>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
        
        <!-- Products Grid -->
        <?php if (empty($products)): ?>
            <div class="no-products">
                <div class="empty-state-icon">🔍</div>
                <h2>No Products Found</h2>
                <p>We couldn't find any products matching your criteria.</p>
                <p>Try adjusting your filters or browse all products.</p>
                <a href="<?php echo BASE_URL; ?>/buyer/shop.php" class="btn btn-primary mt-3">
                    <span>Browse All Products</span>
                </a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <?php include __DIR__ . '/../../views/components/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Load JavaScript -->
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/buyer/cart.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/buyer/shop.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize CartManager
            try {
                window.cartManager = new CartManager();
            } catch (error) {
                console.error('Error initializing cart:', error);
            }
        });
    </script>
    <?php include __DIR__ . '/../../views/layouts/footer.php'; ?>
</body>
</html>