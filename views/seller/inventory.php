<?php
$pageTitle = APP_NAME . ' - Inventory';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
?>

<div class="container mt-4">
    <h1>Inventory Management</h1>

    <?php if (!empty($lowStock)): ?>
    <div class="alert alert-warning">
        <h5>Low Stock Alert!</h5>
        <p>The following products are running low on inventory:</p>
        <ul>
            <?php foreach ($lowStock as $product): ?>
            <li>
                <?php echo htmlspecialchars($product['name']); ?> - 
                <?php echo $product['stock_quantity']; ?> units left
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
    <div class="alert alert-info">
        No products found in inventory.
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Current Stock</th>
                    <th>Min Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                    <td><?php echo $product['stock_quantity']; ?></td>
                    <td><?php echo $product['min_stock_quantity']; ?></td>
                    <td>
                        <span class="badge bg-<?php echo $product['stock_quantity'] > $product['min_stock_quantity'] ? 'success' : 'danger'; ?>">
                            <?php echo $product['stock_quantity'] > $product['min_stock_quantity'] ? 'In Stock' : 'Low Stock'; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary update-stock" data-product-id="<?php echo $product['product_id']; ?>">
                            Update Stock
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