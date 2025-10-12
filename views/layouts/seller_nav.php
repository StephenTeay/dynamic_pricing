<nav style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1rem 0;">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 1.25rem; font-weight: bold; color: var(--primary-color);">
            <a href="/seller/dashboard" style="text-decoration: none; color: inherit;">Seller Dashboard</a>
        </div>
        
        <div style="display: flex; gap: 2rem; align-items: center;">
            <a href="/seller/dashboard" style="text-decoration: none; color: var(--text-secondary);">Dashboard</a>
            <a href="/seller/products" style="text-decoration: none; color: var(--text-secondary);">Products</a>
            <a href="/seller/inventory" style="text-decoration: none; color: var(--text-secondary);">Inventory</a>
            <a href="/seller/pricing" style="text-decoration: none; color: var(--text-secondary);">Pricing</a>
            <a href="/seller/orders" style="text-decoration: none; color: var(--text-secondary);">Orders</a>
            <a href="/seller/analytics" style="text-decoration: none; color: var(--text-secondary);">Analytics</a>
            
            <?php if (\Session::isLoggedIn()): ?>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <span style="color: var(--text-secondary); font-size: 0.875rem;">
                        <?php echo htmlspecialchars(\Session::getUsername()); ?>
                    </span>
                    <a href="/auth/logout" style="text-decoration: none; color: var(--primary-color);">Logout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
