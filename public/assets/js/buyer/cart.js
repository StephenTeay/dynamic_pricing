// public/assets/js/buyer/cart.js

class CartManager {
    static updateQuantity(productId, quantity) {
        const item = Cart.items.find(item => item.productId === productId);
        if (item) {
            item.quantity = parseInt(quantity);
            if (item.quantity <= 0) {
                Cart.removeItem(productId);
            } else {
                Cart.save();
            }
            this.render();
        }
    }
    
    static removeFromCart(productId) {
        Cart.removeItem(productId);
        this.render();
    }
    
    static clearCart() {
        if (confirm('Are you sure you want to clear your cart?')) {
            Cart.clear();
            this.render();
        }
    }
    
    static render() {
        const cartContainer = document.getElementById('cart-container');
        if (!cartContainer) return;
        
        if (Cart.items.length === 0) {
            cartContainer.innerHTML = '<p>Your cart is empty</p>';
            return;
        }
        
        // Render cart items
        let html = '<table><thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr></thead><tbody>';
        
        Cart.items.forEach(item => {
            html += `
                <tr>
                    <td>${item.productId}</td>
                    <td>$0.00</td>
                    <td><input type="number" value="${item.quantity}" onchange="CartManager.updateQuantity(${item.productId}, this.value)"></td>
                    <td>$0.00</td>
                    <td><button onclick="CartManager.removeFromCart(${item.productId})">Remove</button></td>
                </tr>
            `;
        });
        
        html += '</tbody></table>';
        cartContainer.innerHTML = html;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    CartManager.render();
});