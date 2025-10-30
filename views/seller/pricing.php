<?php
$pageTitle = APP_NAME . ' - Pricing';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';

// Add JavaScript file for pricing management
echo '<script src="/dynamic/dynamic_pricing/public/assets/js/seller/pricing-ui.js" defer></script>';
?>

<div class="container mt-4">
    <h1>Pricing Management</h1>

    <?php if (empty($products)): ?>
    <div class="alert alert-info">
        No products found to manage pricing.
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Current Price</th>
                    <th>Cost</th>
                    <th>Margin</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_name'] ?? 'Unnamed Product'); ?></td>
                    <td>₦<?php echo number_format($product['current_price'] ?? 0, 2); ?></td>
                    <td>₦<?php echo number_format($product['base_cost'] ?? 0, 2); ?></td>
                    <td><?php echo number_format($product['margin'] ?? 0, 1); ?>%</td>
                    <td><?php echo $product['last_price_update'] ? date('M j, Y', strtotime($product['last_price_update'])) : 'Never'; ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary update-price" data-product-id="<?php echo $product['product_id']; ?>">
                            Update Price
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>