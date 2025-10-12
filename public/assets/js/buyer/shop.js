// public/assets/js/buyer/shop.js

class Cart {
    static items = [];
    
    static addItem(productId, quantity = 1) {
        const existingItem = this.items.find(item => item.productId === productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.items.push({ productId, quantity });
        }
        
        this.save();
        Toast.show('Added to cart!', 'success');
    }
    
    static removeItem(productId) {
        this.items = this.items.filter(item => item.productId !== productId);
        this.save();
    }
    
    static clear() {
        this.items = [];
        this.save();
    }
    
    static save() {
        // Store in memory (could use localStorage in production)
        window.cartData = JSON.stringify(this.items);
    }
    
    static load() {
        if (window.cartData) {
            this.items = JSON.parse(window.cartData);
        }
    }
    
    static getTotal() {
        return this.items.reduce((total, item) => total + item.quantity, 0);
    }
}

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', () => {
    Cart.load();
});

function viewProduct(productId) {
    window.location.href = `/buyer/product?id=${productId}`;
}

function addToCart(productId) {
    Cart.addItem(productId, 1);
}