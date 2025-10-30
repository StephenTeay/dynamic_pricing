<?php
// Check if editing or creating new product
$isEditing = isset($product);
error_log("Product Form Data: " . print_r($product ?? 'No product data', true));
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
?>

<div class="container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">
            <?php echo $isEditing ? 'Edit Product' : 'Add New Product'; ?>
        </h1>
        <p class="page-subtitle">
            <?php echo $isEditing ? 'Update your product information' : 'Create a new product listing'; ?>
        </p>
    </div>

    <div class="form-section">
        <form action="<?php echo url('seller/product/' . ($isEditing ? 'update/' . $product['product_id'] : 'store')); ?>" 
              method="POST" 
              enctype="multipart/form-data"
              id="product-form">
            
            <!-- Basic Information Section -->
            <div class="form-section-header mb-4">
                <h2>Basic Information</h2>
                <p class="text-secondary">Essential details about your product</p>
            </div>

            <!-- Product Name -->
            <div class="form-group">
                <label for="name" class="form-label">Product Name *</label>
                <input type="text" 
                       class="form-control" 
                       id="name" 
                       name="product_name" 
                       placeholder="Enter product name"
                       value="<?php echo $isEditing ? htmlspecialchars($product['product_name']) : ''; ?>" 
                       required>
                <small class="text-secondary">Choose a clear and descriptive name</small>
            </div>

            <!-- SKU -->
            <div class="form-group">
                <label for="sku" class="form-label">SKU (Stock Keeping Unit) *</label>
                <input type="text" 
                       class="form-control" 
                       id="sku" 
                       name="sku" 
                       placeholder="e.g., PROD-12345"
                       value="<?php echo $isEditing ? htmlspecialchars($product['sku']) : ''; ?>" 
                       required>
                <small class="text-secondary">Unique identifier for inventory tracking</small>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description" class="form-label">Description *</label>
                <textarea class="form-control" 
                          id="description" 
                          name="product_description" 
                          rows="5" 
                          placeholder="Describe your product in detail..."
                          required><?php echo $isEditing ? htmlspecialchars($product['product_description']) : ''; ?></textarea>
                <small class="text-secondary">Provide detailed information about features, benefits, and specifications</small>
            </div>

            <!-- Pricing & Inventory Section -->
            <div class="form-section-header mt-5 mb-4">
                <h2>Pricing & Inventory</h2>
                <p class="text-secondary">Set your pricing and stock levels</p>
            </div>

            <div class="form-row">
                <!-- Price -->
                <div class="form-group">
                    <label for="price" class="form-label">Selling Price ($) *</label>
                    <input type="number" 
                           class="form-control" 
                           id="price" 
                           name="price" 
                           step="0.01" 
                           min="0" 
                           placeholder="0.00"
                           value="<?php echo $isEditing ? number_format($product['current_price'], 2, '.', '') : ''; ?>" 
                           required>
                    <small class="text-secondary">Customer-facing price</small>
                </div>

                <!-- Cost -->
                <div class="form-group">
                    <label for="cost" class="form-label">Base Cost ($) *</label>
                    <input type="number" 
                           class="form-control" 
                           id="cost" 
                           name="cost" 
                           step="0.01" 
                           min="0" 
                           placeholder="0.00"
                           value="<?php echo $isEditing ? number_format($product['base_cost'], 2, '.', '') : ''; ?>" 
                           required>
                    <small class="text-secondary">Your cost per unit</small>
                </div>

                <!-- Initial Stock -->
                <div class="form-group">
                    <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                    <input type="number" 
                           class="form-control" 
                           id="stock_quantity" 
                           name="stock_quantity" 
                           min="0" 
                           placeholder="0"
                           value="<?php echo $isEditing ? $product['quantity_available'] : ''; ?>" 
                           required>
                    <small class="text-secondary">Available units</small>
                </div>
            </div>

            <div class="form-row">
                <!-- Min Stock Level -->
                <div class="form-group">
                    <label for="min_stock_quantity" class="form-label">Low Stock Alert Level</label>
                    <input type="number" 
                           class="form-control" 
                           id="min_stock_quantity" 
                           name="min_stock_quantity" 
                           min="0" 
                           placeholder="5"
                           value="<?php echo $isEditing ? $product['low_stock_threshold'] : '5'; ?>">
                    <small class="text-secondary">Get notified when stock falls below this level</small>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label for="is_active" class="form-label">Product Status</label>
                    <select class="form-control" id="is_active" name="is_active">
                        <option value="1" <?php echo ($isEditing && $product['is_active']) ? 'selected' : ''; ?>>
                            Active (Visible to buyers)
                        </option>
                        <option value="0" <?php echo ($isEditing && !$product['is_active']) ? 'selected' : ''; ?>>
                            Inactive (Hidden from buyers)
                        </option>
                    </select>
                    <small class="text-secondary">Control product visibility</small>
                </div>
            </div>

            <!-- Product Image Section -->
            <div class="form-section-header mt-5 mb-4">
                <h2>Product Image</h2>
                <p class="text-secondary">Upload a high-quality product image</p>
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Product Image</label>
                
                <?php if ($isEditing && !empty($product['image_url'])): ?>
                    <div class="current-image-preview mb-3">
                        <label class="text-secondary mb-2">Current Image:</label>
                        <div class="image-preview-container">
                            <img src="<?php echo $product['image_url']; ?>" 
                                 alt="Current product image" 
                                 class="product-preview-img">
                        </div>
                    </div>
                <?php endif; ?>
                
                <input type="file" 
                       class="form-control" 
                       id="image" 
                       name="image" 
                       accept="image/*">
                
                <?php if ($isEditing): ?>
                    <small class="text-secondary">Leave empty to keep current image</small>
                <?php else: ?>
                    <small class="text-secondary">Recommended: Square image, at least 800x800px, JPG or PNG</small>
                <?php endif; ?>
            </div>

            <!-- Profit Margin Indicator -->
            <div class="profit-indicator mt-4 p-3" id="profit-indicator" style="display: none;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Estimated Profit Margin:</strong>
                        <span id="profit-margin" class="ms-2"></span>
                    </div>
                    <div>
                        <strong>Profit per Unit:</strong>
                        <span id="profit-amount" class="ms-2"></span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions mt-5">
                <a href="<?php echo url('seller/products'); ?>" class="btn btn-secondary">
                    <span>Cancel</span>
                </a>
                <button type="submit" class="btn btn-primary">
                    <span><?php echo $isEditing ? 'Update Product' : 'Create Product'; ?></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Calculate profit margin in real-time
    document.addEventListener('DOMContentLoaded', function() {
        const priceInput = document.getElementById('price');
        const costInput = document.getElementById('cost');
        const profitIndicator = document.getElementById('profit-indicator');
        const profitMargin = document.getElementById('profit-margin');
        const profitAmount = document.getElementById('profit-amount');

        function calculateProfit() {
            const price = parseFloat(priceInput.value) || 0;
            const cost = parseFloat(costInput.value) || 0;

            if (price > 0 && cost > 0) {
                const profit = price - cost;
                const margin = ((profit / price) * 100).toFixed(2);
                
                profitMargin.textContent = margin + '%';
                profitMargin.style.color = margin > 20 ? '#10b981' : margin > 10 ? '#f59e0b' : '#ef4444';
                
                profitAmount.textContent = '$' + profit.toFixed(2);
                profitAmount.style.color = profit > 0 ? '#10b981' : '#ef4444';
                
                profitIndicator.style.display = 'block';
            } else {
                profitIndicator.style.display = 'none';
            }
        }

        priceInput.addEventListener('input', calculateProfit);
        costInput.addEventListener('input', calculateProfit);
        
        // Initial calculation if editing
        calculateProfit();
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>