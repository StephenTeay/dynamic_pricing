<?php
$pageTitle = APP_NAME . ' - Products';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Products</h1>
        <a href="<?php echo url('seller/product/create'); ?>" class="btn btn-primary">Add New Product</a>
    </div>

    <?php if (empty($products)): ?>
    <div class="alert alert-info">
        You haven't added any products yet. Click the "Add New Product" button to get started.
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td><?php echo $product['price_currency']; ?><?php echo number_format($product['current_price'], 2); ?></td>
                    <td><?php echo $product['quantity_available'] ?? 0; ?></td>
                    <td>
                        <span class="badge <?php echo $product['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo url('seller/product/edit/' . $product['product_id']); ?>" class="btn btn-sm btn-primary">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>