// public/assets/js/seller/pricing.js

class PricingManager {
    static async updatePrice(productId) {
        const input = document.querySelector(`input[data-product-id="${productId}"]`);
        if (!input) return;
        
        try {
            await App.put('/pricing', {
                product_id: productId,
                new_price: parseFloat(input.value)
            });
            Toast.show('Price updated', 'success');
        } catch (error) {
            Toast.show('Failed to update price', 'error');
        }
    }
    
    static async createRule(productId, ruleType) {
        const modal = document.getElementById('pricing-modal');
        const formData = new FormData(modal.querySelector('form'));
        
        try {
            await App.post('/pricing/create-rule', {
                product_id: productId,
                rule_type: ruleType,
                ...Object.fromEntries(formData)
            });
            Toast.show('Pricing rule created', 'success');
            modal.style.display = 'none';
        } catch (error) {
            Toast.show('Failed to create rule', 'error');
        }
    }
}