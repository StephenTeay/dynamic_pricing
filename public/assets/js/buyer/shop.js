// public/assets/js/buyer/shop.js
import { Cart } from './cart.js';

// Product detail page navigation
export function viewProduct(productId) {
    const basePath = document.querySelector('meta[name="base-url"]')?.content || '';
    const cleanBasePath = basePath.replace(/\/+$/, '');
    window.location.href = `${cleanBasePath}/buyer/product/${productId}`;
}

// Add to cart functionality - make it available globally for onclick handlers
window.addToCart = function(productId) {
    try {
        // Get quantity from input if it exists, otherwise use 1
        const quantity = document.getElementById('quantity')?.value || 1;
        const cart = Cart.getInstance();
        cart.addItem(parseInt(productId), parseInt(quantity));
        
        // Show success message
        const message = document.createElement('div');
        message.className = 'toast success';
        message.textContent = 'Added to cart successfully!';
        document.body.appendChild(message);
        setTimeout(() => message.remove(), 3000);
    } catch (error) {
        console.error('Error adding to cart:', error);
        
        // Show error message
        const message = document.createElement('div');
        message.className = 'toast error';
        message.textContent = 'Failed to add item to cart. Please try again.';
        document.body.appendChild(message);
        setTimeout(() => message.remove(), 3000);
    }
}

// Cart is initialized by CartManager in shop.php
document.addEventListener('DOMContentLoaded', function() {
    console.log('Shop page initialized');
});