<nav style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1rem 0;">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 1.25rem; font-weight: bold; color: var(--primary-color);">
            <a href="<?php echo url('/buyer/shop'); ?>" style="text-decoration: none; color: inherit;"><?php echo APP_NAME; ?></a>
        </div>
        
        <div style="display: flex; gap: 2rem; align-items: center;">
            <a href="<?php echo url('/buyer/shop'); ?>" style="text-decoration: none; color: var(--text-secondary);">Shop</a>
            <a href="<?php echo url('/buyer/cart'); ?>" style="text-decoration: none; color: var(--text-secondary);">Cart</a>
            <a href="<?php echo url('/buyer/orders'); ?>" style="text-decoration: none; color: var(--text-secondary);">Orders</a>
            
            <?php if (\Session::isLoggedIn()): ?>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <span style="color: var(--text-secondary); font-size: 0.875rem;">
                        Welcome, <?php echo htmlspecialchars(\Session::getUsername()); ?>
                    </span>
                    <a href="<?php echo url('/auth/logout'); ?>" style="text-decoration: none; color: var(--primary-color);">Logout</a>
                </div>
            <?php else: ?>
                <a href="<?php echo url('/login'); ?>" style="text-decoration: none; color: var(--primary-color);">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
