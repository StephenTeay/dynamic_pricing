// public/assets/js/buyer/checkout.js

class Checkout {
    static async processOrder() {
        if (Cart.items.length === 0) {
            Toast.show('Cart is empty', 'warning');
            return;
        }
        
        const form = document.getElementById('checkout-form');
        if (!form) return;
        
        const formData = new FormData(form);
        const orderData = {
            items: Cart.items,
            shipping_address: formData.get('shipping_address'),
            notes: formData.get('notes')
        };
        
        try {
            const response = await App.post('/orders', orderData);
            Toast.show('Order placed successfully!', 'success');
            Cart.clear();
            setTimeout(() => {
                window.location.href = '/buyer/orders';
            }, 1500);
        } catch (error) {
            Toast.show('Failed to place order', 'error');
        }
    }
}