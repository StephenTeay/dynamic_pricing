<?php
// views/components/inventory_alert.php
if (!isset($product)) {
    return;
}
?>

<div style="padding: 1rem; background-color: #fef3c7; border-radius: 0.375rem; margin-bottom: 1rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="font-size: 1.5rem;">⚠️</span>
        <div>
            <strong style="color: #92400e;">Low Stock Alert</strong>
            <p style="margin: 0.25rem 0 0 0; color: #92400e; font-size: 0.875rem;">
                Only <?php echo $product['quantity_available']; ?> units remaining. 
                Low stock threshold: <?php echo $product['low_stock_threshold']; ?>
            </p>
        </div>
        <a href="<?php echo url('/seller/inventory') . '?product_id=' . urlencode($product['product_id']); ?>" 
           style="margin-left: auto;" class="btn btn-sm" style="background-color: #f59e0b; color: white;">
            Restock
        </a>
    </div>
</div>
