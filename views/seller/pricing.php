<?php
$pageTitle = APP_NAME . ' - Pricing';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
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
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td>$<?php echo number_format($product['cost'], 2); ?></td>
                    <td><?php echo number_format((($product['price'] - $product['cost']) / $product['price']) * 100, 1); ?>%</td>
                    <td><?php echo date('M j, Y', strtotime($product['price_updated_at'])); ?></td>
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