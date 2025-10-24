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

    window.renderCart = async function() {
        try {
            const cartItems = await Cart.getItems();
            
            // Show empty cart message if no items
            const cartContainer = document.getElementById('cart-container');
            if (!cartItems || !Array.isArray(cartItems) || cartItems.length === 0) {
                cartContainer.innerHTML = `
                    <div class="empty-cart">
                        <p>Your cart is empty</p>
                        <a href="${BASE_URL}/buyer/shop" class="btn">Continue Shopping</a>
                    </div>`;
                return;
            }

            const html = cartItems.map(item => `
                <div class="cart-item" data-id="${item.id}">
                    <div class="cart-item-image">
                        <img src="${item.image_url}" alt="${item.name}">
                    </div>
                    <div class="cart-item-details">
                        <h3>${item.name}</h3>
                        <p class="price">$${item.price.toFixed(2)}</p>
                        <p class="seller">Sold by: ${item.seller_name}</p>
                        <div class="quantity">
                            <button onclick="updateQuantity(${item.id}, -1)" ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                            <span>${item.quantity}</span>
                            <button onclick="updateQuantity(${item.id}, 1)" ${item.quantity >= item.quantity_available ? 'disabled' : ''}>+</button>
                        </div>
                        <button class="remove" onclick="removeFromCart(${item.id})">Remove</button>
                    </div>
                </div>
            `).join('');

            document.getElementById('cart-container').innerHTML = `
                <div class="cart-items">
                    ${html}
                </div>
                <div class="cart-summary">
                    <div class="subtotal">
                        <span>Subtotal:</span>
                        <span>$${cartItems.reduce((total, item) => total + (item.price * item.quantity), 0).toFixed(2)}</span>
                    </div>
                    <button class="btn checkout" onclick="window.location.href='${BASE_URL}/buyer/checkout'">
                        Proceed to Checkout
                    </button>
                </div>`;
        } catch (error) {
            console.error('Error rendering cart:', error);
            Cart.showToast('Failed to load cart items. Please try again.', 'error');
        }
    };
})();
