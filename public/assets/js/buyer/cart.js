// public/assets/js/buyer/cart.js

(function() {
    // Cart manager
    const Cart = {
        items: [],

        init: function() {
            const savedCart = localStorage.getItem('cart');
            this.items = savedCart ? JSON.parse(savedCart) : [];
            this.updateCartCount();
            console.log('Cart initialized:', this.items);
        },

        getItems: async function() {
            if (!this.items.length) return [];
            
            // Convert cart items to a comma-separated list of IDs
            const ids = this.items.map(item => item.id).join(',');
            
            const response = await fetch(`${BASE_URL}/api/v1/cart?ids=${ids}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) throw new Error('Failed to fetch cart items');
            
            const result = await response.json();
            if (!result.success) throw new Error(result.message || 'Failed to fetch cart items');
            
            // Merge quantities from local cart with product details from API
            return result.data.map(product => ({
                ...product,
                quantity: this.items.find(item => item.id === product.id)?.quantity || 0,
                price: product.current_price
            }));
        },

        addItem: function(productId, quantity = 1) {
            productId = parseInt(productId);
            quantity = parseInt(quantity);
            console.log('Adding to cart:', { productId, quantity });

            const existingItem = this.items.find(item => item.id === productId);
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                this.items.push({ id: productId, quantity: quantity });
            }
            
            this.save();
            this.showToast('Added to cart successfully!', 'success');
        },

        save: function() {
            localStorage.setItem('cart', JSON.stringify(this.items));
            this.updateCartCount();
        },

        updateCartCount: function() {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                const count = this.items.reduce((total, item) => total + item.quantity, 0);
                cartCount.textContent = count;
                cartCount.style.display = count > 0 ? 'block' : 'none';
            }
        },

        showToast: function(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    };

    // Initialize cart when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        Cart.init();
    });

    // Make functions available globally
    window.addToCart = function(productId) {
        try {
            const quantity = document.getElementById('quantity')?.value || 1;
            Cart.addItem(productId, quantity);
        } catch (error) {
            console.error('Error adding to cart:', error);
            Cart.showToast('Failed to add item to cart. Please try again.', 'error');
        }
    };

    window.updateQuantity = function(productId, change) {
        const item = Cart.items.find(item => item.id === productId);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                Cart.items = Cart.items.filter(item => item.id !== productId);
            }
            Cart.save();
            renderCart(); // Re-render cart
        }
    };

    window.removeFromCart = function(productId) {
        Cart.items = Cart.items.filter(item => item.id !== productId);
        Cart.save();
        Cart.showToast('Item removed from cart', 'info');
        renderCart(); // Re-render cart
    };

    window.renderCart = async function() {
        try {
            const cartItems = await Cart.getItems();
            const cartContainer = document.getElementById('cart-container');
            
            // Show empty cart message if no items
            if (!cartItems || !Array.isArray(cartItems) || cartItems.length === 0) {
                cartContainer.innerHTML = `
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <h2>Your Cart is Empty</h2>
                        <p>Looks like you haven't added anything to your cart yet.</p>
                        <p>Start shopping and discover amazing products!</p>
                        <a href="${BASE_URL}/buyer/shop" class="btn btn-primary mt-3">
                            <span>Start Shopping</span>
                        </a>
                    </div>`;
                return;
            }

            // Calculate totals
            const subtotal = cartItems.reduce((total, item) => total + (item.price * item.quantity), 0);
            const tax = subtotal * 0.1; // 10% tax
            const total = subtotal + tax;

            // Render cart items
            const itemsHtml = cartItems.map(item => `
                <div class="cart-item" data-id="${item.id}">
                    <div class="cart-item-image">
                        <img src="${item.image_url}" alt="${item.name}">
                    </div>
                    <div class="cart-item-info">
                        <h3 class="cart-item-name">${item.name}</h3>
                        <p class="product-category">${item.category || 'General'}</p>
                        <p class="cart-item-price">$${item.price.toFixed(2)}</p>
                        <div class="quantity-selector">
                            <label>Quantity:</label>
                            <div class="quantity-controls">
                                <button onclick="updateQuantity(${item.id}, -1)" 
                                        class="btn btn-sm btn-secondary" 
                                        ${item.quantity <= 1 ? 'disabled' : ''}>
                                    -
                                </button>
                                <input type="number" 
                                       value="${item.quantity}" 
                                       class="quantity-input" 
                                       readonly>
                                <button onclick="updateQuantity(${item.id}, 1)" 
                                        class="btn btn-sm btn-secondary"
                                        ${item.quantity >= (item.quantity_available || 999) ? 'disabled' : ''}>
                                    +
                                </button>
                            </div>
                        </div>
                        ${item.seller_name ? `<p class="text-secondary mt-2"><small>Sold by: <strong>${item.seller_name}</strong></small></p>` : ''}
                    </div>
                    <div class="cart-item-actions">
                        <div class="item-total">
                            <strong>$${(item.price * item.quantity).toFixed(2)}</strong>
                        </div>
                        <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">
                            <span>Remove</span>
                        </button>
                    </div>
                </div>
            `).join('');

            cartContainer.innerHTML = `
                <div class="cart-items-section">
                    <h2 class="section-title mb-3">Cart Items (${cartItems.length})</h2>
                    ${itemsHtml}
                </div>
                
                <div class="cart-summary">
                    <h3 class="mb-3">Order Summary</h3>
                    
                    <div class="cart-summary-row">
                        <span>Subtotal (${cartItems.reduce((sum, item) => sum + item.quantity, 0)} items)</span>
                        <span>$${subtotal.toFixed(2)}</span>
                    </div>
                    
                    <div class="cart-summary-row">
                        <span>Tax (10%)</span>
                        <span>$${tax.toFixed(2)}</span>
                    </div>
                    
                    <div class="cart-summary-row">
                        <span>Shipping</span>
                        <span class="text-success"><strong>FREE</strong></span>
                    </div>
                    
                    <div class="cart-summary-row cart-summary-total">
                        <span>Total</span>
                        <span>$${total.toFixed(2)}</span>
                    </div>
                    
                    <button class="btn btn-primary btn-block btn-lg mt-3" 
                            onclick="window.location.href='${BASE_URL}/buyer/checkout'">
                        <span>Proceed to Checkout</span>
                    </button>
                    
                    <a href="${BASE_URL}/buyer/shop" class="btn btn-secondary btn-block mt-2">
                        <span>Continue Shopping</span>
                    </a>
                </div>`;
        } catch (error) {
            console.error('Error rendering cart:', error);
            const cartContainer = document.getElementById('cart-container');
            cartContainer.innerHTML = `
                <div class="cart-error">
                    <div class="error-icon">⚠️</div>
                    <h2>Oops! Something went wrong</h2>
                    <p>We couldn't load your cart items. Please try again.</p>
                    <div class="error-actions">
                        <button onclick="window.location.reload()" class="btn btn-primary">
                            <span>Retry</span>
                        </button>
                        <a href="${BASE_URL}/buyer/shop" class="btn btn-secondary">
                            <span>Continue Shopping</span>
                        </a>
                    </div>
                </div>`;
        }
    };
})();