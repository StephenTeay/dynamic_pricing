<?php
// views/buyer/product.php
if (!defined('BASE_PATH')) {
    exit('No direct script access allowed');
}

// Price history is handled by controller
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - Shop</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/buyer.css">
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/buyer_nav.php'; ?>
    
    <div class="container">
        <div class="product-detail-container">
            <div>
                <div class="product-detail-image">
                    <?php if ($product['image_url']): ?>
                        <img src="<?php 
                            $imgUrl = $product['image_url'];
                            // Remove leading /assets/ or assets/ if present
                            $imgUrl = preg_replace('/^\/?(assets\/)?/', '', $imgUrl);
                            echo asset($imgUrl);
                        ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    <?php else: ?>
                        <img src="<?php echo asset('images/no-image.png'); ?>" alt="No image">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="product-detail-info">
                <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                
                <div style="display: flex; gap: 2rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    <p style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="display: inline-block; width: 8px; height: 8px; background: linear-gradient(135deg, #3b82f6, #6366f1); border-radius: 50%;"></span>
                        <span style="color: #64748b; font-weight: 500; font-size: 0.875rem;">CATEGORY:</span>
                        <strong style="color: #1e293b;"><?php echo htmlspecialchars($product['category']); ?></strong>
                    </p>
                    
                    <p style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="display: inline-block; width: 8px; height: 8px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%;"></span>
                        <span style="color: #64748b; font-weight: 500; font-size: 0.875rem;">SELLER:</span>
                        <strong style="color: #1e293b;"><?php echo isset($product['business_name']) ? htmlspecialchars($product['business_name']) : 'Unknown Seller'; ?></strong>
                    </p>
                </div>
                
                <div class="product-detail-price">
                    ₦<?php echo number_format($product['current_price'], 2); ?>
                </div>
                
                <p class="product-detail-description">
                    <?php echo nl2br(htmlspecialchars($product['product_description'])); ?>
                </p>
                
                <div style="margin-bottom: 2rem; padding: 1.25rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 0.75rem; border-left: 4px solid <?php echo (isset($product['quantity_available']) && $product['quantity_available'] > 0) ? '#10b981' : '#ef4444'; ?>;">
                    <?php 
                    $inStock = isset($product['quantity_available']) && $product['quantity_available'] > 0;
                    $lowStock = isset($product['quantity_available']) && isset($product['low_stock_threshold']) && 
                               $product['quantity_available'] <= $product['low_stock_threshold'] && 
                               $product['quantity_available'] > 0;
                    ?>
                    
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: <?php echo $lowStock ? '0.75rem' : '0'; ?>;">
                        <?php if ($inStock): ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span style="color: #10b981; font-weight: 700; font-size: 1.125rem;">In Stock</span>
                        <?php else: ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            <span style="color: #ef4444; font-weight: 700; font-size: 1.125rem;">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($lowStock): ?>
                        <div style="display: flex; align-items: center; gap: 0.5rem; padding-left: 2rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <span style="color: #f59e0b; font-weight: 600; font-size: 0.9375rem;">
                                Only <?php echo $product['quantity_available']; ?> units remaining
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="quantity-selector">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" class="quantity-input form-control" 
                           value="1" min="1" 
                           max="<?php echo isset($product['quantity_available']) ? $product['quantity_available'] : 0; ?>"
                           <?php echo !$inStock ? 'disabled' : ''; ?>>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
                    <?php if ($inStock): ?>
                        <button onclick="addToCart(<?php echo $product['product_id']; ?>)" 
                                class="btn btn-primary btn-lg" style="flex: 1;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.5rem;">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            Add to Cart
                        </button>
                        <button onclick="addToWishlist(<?php echo $product['product_id']; ?>)" 
                                class="btn btn-secondary" style="min-width: 120px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                            Wishlist
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg" disabled style="flex: 1;">
                            Out of Stock
                        </button>
                    <?php endif; ?>
                </div>
                
                <div style="padding: 2rem; background: white; border-radius: 0.75rem; border: 2px solid #e2e8f0;">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        Product Details
                    </h3>
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 1rem 0; color: #64748b; font-weight: 600; width: 40%;">SKU</td>
                            <td style="padding: 1rem 0; color: #1e293b; font-weight: 500;"><?php echo htmlspecialchars($product['sku']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 1rem 0; color: #64748b; font-weight: 600;">Availability</td>
                            <td style="padding: 1rem 0;">
                                <?php 
                                $quantity = isset($product['quantity_available']) ? $product['quantity_available'] : 0;
                                $threshold = isset($product['low_stock_threshold']) ? $product['low_stock_threshold'] : 5;
                                
                                if ($quantity > $threshold) {
                                    echo '<span class="badge" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 0.375rem;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background: #10b981; border-radius: 50%;"></span>
                                        In Stock
                                    </span>';
                                } elseif ($quantity > 0) {
                                    echo '<span class="badge" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 0.375rem;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background: #f59e0b; border-radius: 50%;"></span>
                                        Limited Stock
                                    </span>';
                                } else {
                                    echo '<span class="badge" style="background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 0.375rem;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background: #ef4444; border-radius: 50%;"></span>
                                        Out of Stock
                                    </span>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Price History -->
        <?php if (!empty($priceHistory)): ?>
        <div style="margin-top: 3rem; background: white; padding: 2.5rem; border-radius: 1.25rem; box-shadow: 0 10px 15px rgba(15, 23, 42, 0.1), 0 4px 6px rgba(15, 23, 42, 0.05); border: 1px solid rgba(226, 232, 240, 0.6);">
            <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 2rem; color: #1e293b; display: flex; align-items: center; gap: 0.75rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                Price History
                <span style="font-size: 0.875rem; font-weight: 500; color: #64748b; background: #f1f5f9; padding: 0.375rem 0.75rem; border-radius: 0.5rem;">(Last 10 Changes)</span>
            </h2>
            <div class="table-responsive" style="overflow-x: auto; border-radius: 0.75rem; border: 2px solid #e2e8f0;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                            <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 700; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;">Date</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 700; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;">Old Price</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 700; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;">New Price</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 700; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;">Change</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 700; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;">Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($priceHistory as $index => $history): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s ease; <?php echo $index % 2 === 0 ? 'background: #fafbfc;' : 'background: white;'; ?>">
                            <td style="padding: 1rem 1.5rem; color: #64748b; font-weight: 500; font-size: 0.9375rem;"><?php echo date('M d, Y H:i', strtotime($history['changed_at'])); ?></td>
                            <td style="padding: 1rem 1.5rem; color: #1e293b; font-weight: 600; font-size: 1rem;">₦<?php echo number_format($history['old_price'], 2); ?></td>
                            <td style="padding: 1rem 1.5rem; color: #1e293b; font-weight: 700; font-size: 1rem;">₦<?php echo number_format($history['new_price'], 2); ?></td>
                            <td style="padding: 1rem 1.5rem;">
                                <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.875rem; <?php echo $history['price_change_percent'] >= 0 ? 'background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46;' : 'background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b;'; ?>">
                                    <?php if ($history['price_change_percent'] >= 0): ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="19" x2="12" y2="5"></line>
                                            <polyline points="5 12 12 5 19 12"></polyline>
                                        </svg>
                                    <?php else: ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <polyline points="19 12 12 19 5 12"></polyline>
                                        </svg>
                                    <?php endif; ?>
                                    <?php echo $history['price_change_percent'] >= 0 ? '+' : ''; ?>
                                    <?php echo number_format($history['price_change_percent'], 2); ?>%
                                </span>
                            </td>
                            <td style="padding: 1rem 1.5rem; color: #64748b; font-weight: 500; font-size: 0.9375rem;"><?php echo htmlspecialchars($history['change_reason']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/buyer/shop.js"></script>
    <script>
        function addToCart(productId) {
            const quantity = parseInt(document.getElementById('quantity').value);
            Cart.addItem(productId, quantity);
        }
        
        function addToWishlist(productId) {
            // Placeholder for wishlist functionality
            Toast.show('Added to wishlist!', 'success');
        }
    </script>
</body>
</html>