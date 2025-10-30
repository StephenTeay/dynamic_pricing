<?php
$pageTitle = APP_NAME . ' - Products';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }
    
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        margin: -2.5rem -1.5rem 3rem;
        border-radius: 0 0 2rem 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
        animation: float 8s ease-in-out infinite;
    }
    
    .page-header-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 2rem;
    }
    
    .page-title-wrapper {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }
    
    .page-icon {
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.25);
        border-radius: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(15px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .page-title {
        font-size: 2.75rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -1px;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    .page-subtitle {
        font-size: 1.0625rem;
        opacity: 0.95;
        font-weight: 500;
        margin-top: 0.375rem;
    }
    
    .header-actions .btn {
        background: white;
        color: #667eea;
        font-weight: 700;
        padding: 0.875rem 2rem;
        font-size: 1rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .header-actions .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25);
        background: white;
        color: #667eea;
    }
    
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .stat-card {
        background: white;
        padding: 1.75rem 2rem;
        border-radius: 1.25rem;
        box-shadow: 0 4px 6px rgba(15, 23, 42, 0.07), 0 2px 4px rgba(15, 23, 42, 0.06);
        border: 2px solid #f1f5f9;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out backwards;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
        border-color: rgba(102, 126, 234, 0.3);
    }
    
    .stat-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .stat-value {
        font-size: 2.25rem;
        font-weight: 900;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }
    
    .alert-info {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-left: 5px solid #3b82f6;
        border-radius: 1rem;
        padding: 2rem 2.25rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        animation: slideInRight 0.5s ease-out;
        color: #1e40af;
        font-weight: 600;
        font-size: 1.0625rem;
    }
    
    .alert-info svg {
        flex-shrink: 0;
    }
    
    .products-section {
        background: white;
        padding: 2.5rem;
        border-radius: 1.5rem;
        box-shadow: 0 4px 6px rgba(15, 23, 42, 0.07), 0 2px 4px rgba(15, 23, 42, 0.06);
        border: 2px solid #f1f5f9;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    
    .section-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }
    
    .search-filter {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .search-input {
        padding: 0.75rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.9375rem;
        min-width: 250px;
        transition: all 0.2s ease;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .filter-select {
        padding: 0.75rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.9375rem;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .filter-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .table-responsive {
        overflow-x: auto;
        border-radius: 1rem;
        border: 2px solid #e2e8f0;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table thead {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    .table th {
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-weight: 800;
        color: #1e293b;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .table td {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        font-weight: 500;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    .product-id {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        color: #64748b;
        font-size: 0.875rem;
    }
    
    .product-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 1rem;
    }
    
    .product-price {
        font-weight: 800;
        font-size: 1.125rem;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 0.875rem;
        border-radius: 0.5rem;
        font-weight: 700;
        font-size: 0.875rem;
    }
    
    .stock-badge.high {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }
    
    .stock-badge.medium {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }
    
    .stock-badge.low {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }
    
    .badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 700;
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-success {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        color: #065f46;
    }
    
    .badge-danger {
        background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
        color: #7f1d1d;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border: none;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.875rem;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.8125rem;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
    }
    
    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(107, 114, 128, 0.3);
        color: white;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
        color: white;
    }
    
    .no-products {
        text-align: center;
        padding: 5rem 2rem;
    }
    
    .empty-icon {
        font-size: 6rem;
        margin-bottom: 1.5rem;
        opacity: 0.3;
        animation: float 3s ease-in-out infinite;
    }
    
    .no-products h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
    }
    
    .no-products p {
        color: #64748b;
        font-size: 1.0625rem;
        margin-bottom: 2rem;
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 2rem 1.5rem;
            margin: -2.5rem -1rem 2rem;
        }
        
        .page-title {
            font-size: 2rem;
        }
        
        .page-icon {
            width: 60px;
            height: 60px;
        }
        
        .page-icon svg {
            width: 28px;
            height: 28px;
        }
        
        .stats-overview {
            grid-template-columns: 1fr;
        }
        
        .search-filter {
            flex-direction: column;
            width: 100%;
        }
        
        .search-input {
            width: 100%;
            min-width: auto;
        }
        
        .table {
            font-size: 0.875rem;
        }
        
        .table th,
        .table td {
            padding: 1rem;
        }
        
        .action-buttons {
            flex-direction: column;
        }
    }
</style>

<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title-wrapper">
                <div class="page-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
                <div>
                    <h1 class="page-title">Products</h1>
                    <p class="page-subtitle">Manage your product catalog</p>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="<?php echo url('seller/product/create'); ?>" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add New Product
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty($products)): ?>
    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-label">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                </svg>
                Total Products
            </div>
            <div class="stat-value" data-count="<?php echo count($products); ?>">0</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Active
            </div>
            <div class="stat-value" data-count="<?php echo count(array_filter($products, function($p) { return $p['is_active']; })); ?>">0</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                </svg>
                In Stock
            </div>
            <div class="stat-value" data-count="<?php echo count(array_filter($products, function($p) { return ($product['quantity_available']  ?? 0) > 0; })); ?>">0</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                Total Value
            </div>
            <div class="stat-value" data-count="<?php echo array_sum(array_map(function($p) { return $p['current_price'] * ($p['quantity_available'] ?? 0); }, $products)); ?>">0</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Products Section -->
    <div class="products-section">
        <?php if (empty($products)): ?>
        <div class="no-products">
            <div class="empty-icon">📦</div>
            <h2>No Products Yet</h2>
            <p>You haven't added any products yet. Click the button below to create your first product.</p>
            <a href="<?php echo url('seller/product/create'); ?>" class="btn btn-primary btn-lg">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Your First Product
            </a>
        </div>
        <?php else: ?>
        <div class="section-header">
            <h2 class="section-title">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"></line>
                    <line x1="8" y1="12" x2="21" y2="12"></line>
                    <line x1="8" y1="18" x2="21" y2="18"></line>
                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
                Product List
            </h2>
            
            <div class="search-filter">
                <input type="text" 
                       id="product-search" 
                       class="search-input" 
                       placeholder="🔍 Search products..."
                       onkeyup="filterProducts()">
                <select id="status-filter" class="filter-select" onchange="filterProducts()">
                    <option value="">All Status</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table" id="products-table">
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
                    <?php
                        $stock = $product['quantity_available'] ?? 0;
                        $stockClass = $stock > 50 ? 'high' : ($stock > 10 ? 'medium' : 'low');
                    ?>
                    <tr data-status="<?php echo $product['is_active'] ? 'active' : 'inactive'; ?>">
                        <td class="product-id"><?php echo htmlspecialchars($product['product_id']); ?></td>
                        <td class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td class="product-price"><?php echo $product['price_currency']; ?><?php echo number_format($product['current_price'], 2); ?></td>
                        <td>
                            <span class="stock-badge <?php echo $stockClass; ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="1" y="3" width="15" height="13"></rect>
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                </svg>
                                <?php echo $stock; ?> units
                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo $product['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?php echo url('seller/product/edit/' . $product['product_id']); ?>" class="btn btn-sm btn-primary">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <a href="<?php echo url('buyer/product/' . $product['product_id']); ?>" class="btn btn-sm btn-secondary" target="_blank">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    View
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    'use strict';

    // Animate stat counters
    function animateCounter(element, start, end, duration) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= end) {
                current = end;
                clearInterval(timer);
            }
            
            // Format numbers with commas
            const formatted = Math.floor(current).toLocaleString();
            element.textContent = formatted;
        }, 16);
    }

    // Initialize counter animations
    function initCounters() {
        const statValues = document.querySelectorAll('.stat-value');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const targetValue = parseInt(element.getAttribute('data-count')) || 0;
                    
                    animateCounter(element, 0, targetValue, 2000);
                    observer.unobserve(element);
                }
            });
        }, { threshold: 0.5 });
        
        statValues.forEach(stat => observer.observe(stat));
    }

    // Filter products
    window.filterProducts = function() {
        const searchInput = document.getElementById('product-search');
        const statusFilter = document.getElementById('status-filter');
        const table = document.getElementById('products-table');
        const rows = table.querySelectorAll('tbody tr');
        
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const statusValue = statusFilter ? statusFilter.value : '';
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            const productName = row.querySelector('.product-name').textContent.toLowerCase();
            const productId = row.querySelector('.product-id').textContent.toLowerCase();
            const rowStatus = row.getAttribute('data-status');
            
            const matchesSearch = productName.includes(searchTerm) || productId.includes(searchTerm);
            const matchesStatus = !statusValue || rowStatus === statusValue;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show "no results" message if needed
        const tbody = table.querySelector('tbody');
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem; opacity: 0.5;">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <div style="font-weight: 600; font-size: 1.125rem; margin-bottom: 0.5rem;">No products found</div>
                        <div>Try adjusting your search or filter criteria</div>
                    </td>
                `;
                tbody.appendChild(noResultsRow);
            }
            noResultsRow.style.display = '';
        } else if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
    };

    // Add row hover effects with smooth transitions
    function enhanceTableRows() {
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach((row, index) => {
            row.style.animation = `fadeInUp 0.4s ease-out ${index * 0.05}s backwards`;
            
            // Add click to expand/highlight effect
            row.addEventListener('click', function(e) {
                // Don't trigger if clicking on buttons
                if (e.target.closest('.btn')) return;
                
                // Remove highlight from other rows
                rows.forEach(r => r.style.background = '');
                
                // Highlight clicked row briefly
                this.style.background = 'linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%)';
                
                setTimeout(() => {
                    this.style.background = '';
                }, 2000);
            });
        });
    }

    // Initialize everything when DOM is ready
    function init() {
        initCounters();
        
        // Only enhance table if it exists
        const table = document.getElementById('products-table');
        if (table) {
            enhanceTableRows();
        }
        
        // Add keyboard shortcut for search (Ctrl/Cmd + K)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('product-search');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
    }

    // Run initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>