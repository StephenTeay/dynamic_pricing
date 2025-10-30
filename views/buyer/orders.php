<?php
// views/buyer/orders.php
if (!defined('BASE_PATH')) {
    exit('No direct script access allowed');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo BASE_URL; ?>">
    <title>My Orders - Dynamic Pricing</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/main.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/buyer.css">
</head>
<body>
    <?php include __DIR__ . '/../../views/layouts/buyer_nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1 class="page-title" style="display: flex; align-items: center; justify-content: center; gap: 1rem;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="url(#orderGradient)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <defs>
                        <linearGradient id="orderGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#6366f1;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                My Orders
            </h1>
            <p class="page-subtitle">Track and manage all your purchases</p>
        </div>
        
        <?php if (empty($orders)): ?>
            <div class="empty-cart" style="padding: 5rem 2rem;">
                <div class="empty-cart-icon">
                    <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                </div>
                <h2 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">No Orders Yet</h2>
                <p style="font-size: 1.125rem; color: #64748b; margin-bottom: 0.5rem;">You haven't placed any orders yet.</p>
                <p style="font-size: 1rem; color: #94a3b8; margin-bottom: 2.5rem;">Start shopping and discover amazing products!</p>
                <a href="<?php echo BASE_URL; ?>/buyer/shop" class="btn btn-primary btn-lg" style="display: inline-flex; align-items: center; gap: 0.75rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    Start Shopping
                </a>
            </div>
        <?php else: ?>
            <div style="margin-bottom: 2rem; padding: 1.25rem 1.75rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 0.75rem; border-left: 4px solid #3b82f6; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="12" y1="18" x2="12" y2="12"></line>
                        <line x1="9" y1="15" x2="15" y2="15"></line>
                    </svg>
                    <span style="font-weight: 600; color: #1e293b; font-size: 1rem;">
                        You have <strong style="color: #3b82f6; font-size: 1.125rem;"><?php echo count($orders); ?></strong> order<?php echo count($orders) !== 1 ? 's' : ''; ?>
                    </span>
                </div>
            </div>

            <div class="orders-list" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card" style="background: white; border-radius: 1rem; box-shadow: 0 4px 6px rgba(15, 23, 42, 0.07), 0 2px 4px rgba(15, 23, 42, 0.06); border: 1px solid rgba(226, 232, 240, 0.6); overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                        <div class="order-header" style="padding: 1.75rem 2rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="1" y="3" width="15" height="13"></rect>
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                </svg>
                                <div>
                                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">
                                        Order #<?php echo htmlspecialchars($order['id']); ?>
                                    </h3>
                                    <span class="order-date" style="font-size: 0.875rem; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.375rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                            <span class="order-status" style="padding: 0.625rem 1.25rem; border-radius: 2rem; font-weight: 700; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 0.5rem; 
                                <?php 
                                $status = strtolower($order['status']);
                                if ($status === 'completed' || $status === 'delivered') {
                                    echo 'background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46;';
                                } elseif ($status === 'pending') {
                                    echo 'background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e;';
                                } elseif ($status === 'processing' || $status === 'shipped') {
                                    echo 'background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af;';
                                } elseif ($status === 'cancelled') {
                                    echo 'background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b;';
                                } else {
                                    echo 'background: linear-gradient(135deg, #f1f5f9, #e2e8f0); color: #475569;';
                                }
                                ?>">
                                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%;
                                    <?php 
                                    if ($status === 'completed' || $status === 'delivered') {
                                        echo 'background: #10b981;';
                                    } elseif ($status === 'pending') {
                                        echo 'background: #f59e0b;';
                                    } elseif ($status === 'processing' || $status === 'shipped') {
                                        echo 'background: #3b82f6;';
                                    } elseif ($status === 'cancelled') {
                                        echo 'background: #ef4444;';
                                    } else {
                                        echo 'background: #64748b;';
                                    }
                                    ?>">
                                </span>
                                <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                            </span>
                        </div>
                        
                        <div class="order-items" style="padding: 2rem;">
                            <?php foreach ($order['items'] as $index => $item): ?>
                                <div class="order-item" style="display: grid; grid-template-columns: 100px 1fr auto; gap: 1.5rem; align-items: center; padding: 1.25rem; <?php echo $index < count($order['items']) - 1 ? 'border-bottom: 1px solid #f1f5f9;' : ''; ?> margin-bottom: <?php echo $index < count($order['items']) - 1 ? '1rem' : '0'; ?>; border-radius: 0.75rem; transition: all 0.2s ease;">
                                    <img src="<?php echo ASSETS_URL; ?>/images/products/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                         class="item-image"
                                         style="width: 100px; height: 100px; border-radius: 0.75rem; object-fit: cover; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.1); border: 2px solid #f1f5f9;">
                                    <div class="item-details">
                                        <h4 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; line-height: 1.4;">
                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                        </h4>
                                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                                            <p style="margin: 0; display: flex; align-items: center; gap: 0.5rem; font-size: 0.9375rem;">
                                                <span style="color: #64748b; font-weight: 500;">Quantity:</span>
                                                <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); border-radius: 0.5rem; font-weight: 700; color: #1e293b; padding: 0 0.5rem;">
                                                    <?php echo htmlspecialchars($item['quantity']); ?>
                                                </span>
                                            </p>
                                            <p style="margin: 0; display: flex; align-items: center; gap: 0.5rem; font-size: 0.9375rem;">
                                                <span style="color: #64748b; font-weight: 500;">Unit Price:</span>
                                                <span style="font-weight: 700; color: #3b82f6; font-size: 1rem;">
                                                    $<?php echo number_format($item['price'], 2); ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.375rem;">Subtotal</div>
                                        <div style="font-size: 1.5rem; font-weight: 800; background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                            $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-footer" style="padding: 1.75rem 2rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-top: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
                            <div style="display: flex; gap: 1rem;">
                                <a href="<?php echo BASE_URL; ?>/buyer/order/<?php echo $order['id']; ?>" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 16 16 12 12 8"></polyline>
                                        <line x1="8" y1="12" x2="16" y2="12"></line>
                                    </svg>
                                    View Details
                                </a>
                                <?php if (strtolower($order['status']) === 'delivered' || strtolower($order['status']) === 'completed'): ?>
                                <button class="btn btn-success" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                                    </svg>
                                    Review Order
                                </button>
                                <?php endif; ?>
                            </div>
                            <div class="order-total" style="display: flex; flex-direction: column; align-items: flex-end;">
                                <span style="font-size: 0.875rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.375rem;">Order Total</span>
                                <span style="font-size: 2rem; font-weight: 900; background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1; letter-spacing: -0.5px;">
                                    $<?php echo number_format($order['total_amount'], 2); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="<?php echo ASSETS_URL; ?>/js/api.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/app.js"></script>
</body>
</html>