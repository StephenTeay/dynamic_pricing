// cart.module.js

// Cart singleton object
const Cart = {
    items: [],
    
    init() {
        const savedCart = localStorage.getItem('cart');
        this.items = savedCart ? JSON.parse(savedCart) : [];
        this.updateCartCount();
    },
    
    save() {
        localStorage.setItem('cart', JSON.stringify(this.items));
        this.updateCartCount();
    },
    
    addItem(productId, quantity = 1) {
        const existingItem = this.items.find(item => item.id === productId);
        if (existingItem) {
            existingItem.quantity += parseInt(quantity);
        } else {
            this.items.push({ id: parseInt(productId), quantity: parseInt(quantity) });
        }
        this.save();
    },
    
    updateItem(productId, quantity) {
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = parseInt(quantity);
            this.save();
        }
    },
    
    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== parseInt(productId));
        this.save();
    },
    
    clear() {
        this.items = [];
        this.save();
    },
    
    updateCartCount() {
        const count = this.items.reduce((total, item) => total + item.quantity, 0);
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = count;
            cartCount.style.display = count > 0 ? 'inline' : 'none';
        }
    },

    getItemCount() {
        return this.items.reduce((total, item) => total + item.quantity, 0);
    },

    getItems() {
        return this.items;
    }
};

// CartManager singleton object
const CartManager = {
    init() {
        Cart.init();
        return Promise.resolve();
    },

    getBaseUrl() {
        const metaTag = document.querySelector('meta[name="base-url"]');
        return metaTag ? metaTag.getAttribute('content') : '';
    },

    async fetchProductDetails(productIds) {
        if (!productIds.length) return [];
        
        try {
            const baseUrl = this.getBaseUrl();
            const response = await fetch(`${baseUrl}/api/v1/cart?ids=${productIds.join(',')}`);
            if (!response.ok) throw new Error('Failed to fetch products');
            const result = await response.json();
            return result.data;
        } catch (error) {
            console.error('Error fetching products:', error);
            throw error;
        }
    },
    
    async updateQuantity(productId, quantity) {
        const newQuantity = parseInt(quantity);
        if (newQuantity <= 0) {
            Cart.removeItem(productId);
        } else {
            Cart.updateItem(productId, newQuantity);
        }
        await this.render();
    },
    
    removeFromCart(productId) {
        Cart.removeItem(productId);
        this.render();
    },
    
    clearCart() {
        if (confirm('Are you sure you want to clear your cart?')) {
            Cart.clear();
            this.render();
        }
    },
    
    formatPrice(price) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(price);
    },
    
    async render() {
        const cartContainer = document.getElementById('cart-container');
        if (!cartContainer) return;
        
        const items = Cart.getItems();
        
        if (items.length === 0) {
            cartContainer.innerHTML = `
                <div class="empty-cart">
                    <p>Your cart is empty</p>
                    <a href="${this.getBaseUrl()}/buyer/shop" class="btn">Continue Shopping</a>
                </div>`;
            return;
        }
        
        try {
            const productIds = items.map(item => item.id);
            const products = await this.fetchProductDetails(productIds);
            
            if (!products.length) {
                Cart.clear();
                cartContainer.innerHTML = `
                    <div class="cart-error">
                        <p>Products in your cart are no longer available.</p>
                        <a href="${this.getBaseUrl()}/buyer/shop" class="btn">Continue Shopping</a>
                    </div>`;
                return;
            }
            
            let subtotal = 0;
            let cartHtml = `
                <div class="cart-items">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>`;
            
            products.forEach(product => {
                const cartItem = items.find(item => item.id === product.id);
                if (!cartItem) return;
                
                const itemTotal = product.current_price * cartItem.quantity;
                subtotal += itemTotal;
                
                cartHtml += `
                    <tr>
                        <td>
                            <a href="${this.getBaseUrl()}/buyer/product/${product.id}">${product.name}</a>
                        </td>
                        <td>${this.formatPrice(product.current_price)}</td>
                        <td>
                            <input type="number" 
                                   min="1" 
                                   max="${product.inventory_count}"
                                   value="${cartItem.quantity}"
                                   onchange="window.cartManager.updateQuantity(${product.id}, this.value)">
                            <span class="stock-info">${product.inventory_count} available</span>
                        </td>
                        <td>${this.formatPrice(itemTotal)}</td>
                        <td>
                            <button onclick="window.cartManager.removeFromCart(${product.id})" class="btn-remove">
                                Remove
                            </button>
                        </td>
                    </tr>`;
            });
            
            cartHtml += `
                    </tbody>
                </table>
            </div>
            <div class="cart-summary">
                <p>Subtotal: <strong>${this.formatPrice(subtotal)}</strong></p>
                <div class="cart-actions">
                    <button onclick="window.cartManager.clearCart()" class="btn">Clear Cart</button>
                    <a href="${this.getBaseUrl()}/buyer/checkout" class="btn btn-primary">Proceed to Checkout</a>
                </div>
            </div>`;
            
            cartContainer.innerHTML = cartHtml;
            
        } catch (error) {
            console.error('Error rendering cart:', error);
            cartContainer.innerHTML = `
                <div class="cart-error">
                    <p>Failed to load cart items. Please try again.</p>
                    <button onclick="window.cartManager.render()" class="btn">Retry</button>
                </div>`;
        }
    }
};

// Export the objects
export { Cart, CartManager };