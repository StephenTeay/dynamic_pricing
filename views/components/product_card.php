<div class="product-card">
    <div class="product-image">
        <?php if (isset($product['image_url']) && $product['image_url']): ?>
            <img src="<?php 
                $imgUrl = $product['image_url'];
                // Remove leading /assets/ or assets/ if present
                $imgUrl = preg_replace('/^\/?(assets\/)?/', '', $imgUrl);
                echo asset($imgUrl); 
            ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
        <?php else: ?>
            <img src="<?php echo asset('images/no-image.png'); ?>" alt="No image">
        <?php endif; ?>
        
        <?php if (isset($product['quantity_available']) && $product['quantity_available'] <= $product['low_stock_threshold']): ?>
            <span class="badge" style="position: absolute; top: 10px; right: 10px; background-color: #f59e0b; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem;">
                Low Stock
            </span>
        <?php endif; ?>
    </div>
    
    <div class="product-info">
        <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
        <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
        
        <div class="product-price">
            ₦<?php echo number_format($product['current_price'], 2); ?>
        </div>
        
        <div class="product-actions">
            <a href="<?php echo url('buyer/product/' . $product['product_id']); ?>" class="btn btn-primary">
                View
            </a>
            <button class="btn btn-secondary" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                Add to Cart
            </button>
        </div>
    </div>
</div>
