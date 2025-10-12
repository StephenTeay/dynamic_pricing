// public/assets/js/seller/inventory.js

class InventoryManager {
    static async updateStock(productId, quantity) {
        try {
            await App.put(`/inventory/${productId}`, { quantity });
            Toast.show('Inventory updated', 'success');
        } catch (error) {
            Toast.show('Failed to update inventory', 'error');
        }
    }
    
    static async adjustStock(productId, adjustment) {
        try {
            await App.put(`/inventory/${productId}`, { adjustment });
            Toast.show('Stock adjusted', 'success');
        } catch (error) {
            Toast.show('Failed to adjust stock', 'error');
        }
    }
}