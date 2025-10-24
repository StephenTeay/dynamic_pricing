<?php
// Check if editing or creating new product
$isEditing = isset($product);
error_log("Product Form Data: " . print_r($product ?? 'No product data', true));
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title h4 mb-0">
                        <?php echo $isEditing ? 'Edit Product' : 'Add New Product'; ?>
                    </h1>
                </div>
                <div class="card-body">
                    <form action="<?php echo url('seller/product/' . ($isEditing ? 'update/' . $product['product_id'] : 'store')); ?>" 
                          method="POST" 
                          enctype="multipart/form-data">
                        
                        <!-- Product Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="product_name" 
                                   value="<?php echo $isEditing ? htmlspecialchars($product['product_name']) : ''; ?>" 
                                   required>
                        </div>

                        <!-- SKU -->
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="sku" 
                                   name="sku" 
                                   value="<?php echo $isEditing ? htmlspecialchars($product['sku']) : ''; ?>" 
                                   required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="product_description" 
                                      rows="4" 
                                      required><?php echo $isEditing ? htmlspecialchars($product['product_description']) : ''; ?></textarea>
                        </div>

                        <div class="row">
                            <!-- Price -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($) *</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="price" 
                                           name="price" 
                                           step="0.01" 
                                           min="0" 
                                           value="<?php echo $isEditing ? number_format($product['current_price'], 2) : ''; ?>" 
                                           required>
                                </div>
                            </div>

                            <!-- Cost -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cost" class="form-label">Cost ($) *</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="cost" 
                                           name="cost" 
                                           step="0.01" 
                                           min="0" 
                                           value="<?php echo $isEditing ? number_format($product['base_cost'], 2) : ''; ?>" 
                                           required>
                                </div>
                            </div>

                            <!-- Initial Stock -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="stock_quantity" 
                                           name="stock_quantity" 
                                           min="0" 
                                           value="<?php echo $isEditing ? $product['quantity_available'] : ''; ?>" 
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Min Stock Level -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_stock_quantity" class="form-label">Minimum Stock Level</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="min_stock_quantity" 
                                           name="min_stock_quantity" 
                                           min="0" 
                                           value="<?php echo $isEditing ? $product['low_stock_threshold'] : '5'; ?>">
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Status</label>
                                    <select class="form-select" id="is_active" name="is_active">
                                        <option value="1" <?php echo ($isEditing && $product['is_active']) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($isEditing && !$product['is_active']) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Product Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <?php if ($isEditing && !empty($product['image_url'])): ?>
                                <div class="mb-2">
                                    <img src="<?php echo $product['image_url']; ?>" 
                                         alt="Current product image" 
                                         style="max-width: 200px;" 
                                         class="img-thumbnail">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <?php if ($isEditing): ?>
                                <small class="form-text text-muted">Leave empty to keep current image</small>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('seller/products'); ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <?php echo $isEditing ? 'Update Product' : 'Create Product'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>