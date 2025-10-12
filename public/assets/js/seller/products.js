// public/assets/js/seller/products.js

class ProductManager {
    static async deleteProduct(productId) {
        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }
        
        try {
            await App.delete(`/products/${productId}`);
            Toast.show('Product deleted', 'success');
            setTimeout(() => location.reload(), 1000);
        } catch (error) {
            Toast.show('Failed to delete product', 'error');
        }
    }
    
    static async toggleActive(productId, isActive) {
        try {
            await App.put(`/products/${productId}`, { is_active: !isActive });
            Toast.show('Product status updated', 'success');
            setTimeout(() => location.reload(), 1000);
        } catch (error) {
            Toast.show('Failed to update product', 'error');
        }
    }
}