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
    <title>Shop - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/buyer.css">
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/buyer_nav.php'; ?>
    
    <div class="container">
        <h1>Shop</h1>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search products..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                </div>
                
                <div class="filter-group">
                    <select name="category" class="form-control">
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
                    <input type="number" name="min_price" class="form-control" 
                           placeholder="Min Price" value="<?php echo htmlspecialchars($minPrice ?? ''); ?>">
                </div>
                
                <div class="filter-group">
                    <input type="number" name="max_price" class="form-control" 
                           placeholder="Max Price" value="<?php echo htmlspecialchars($maxPrice ?? ''); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">Filter</button>
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
    
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/buyer/shop.js"></script>
</body>
</html>
