// public/assets/js/seller/inventory-ui.js

document.addEventListener('DOMContentLoaded', function() {
    // Handle stock update button clicks
    document.querySelectorAll('.update-stock').forEach(button => {
        button.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            const currentStock = parseInt(this.dataset.currentStock) || 0;
            
            // Show modal with current stock value
            const newStock = window.prompt('Enter new stock quantity:', currentStock);
            
            if (newStock === null) return; // User cancelled
            
            const quantity = parseInt(newStock);
            if (isNaN(quantity) || quantity < 0) {
                alert('Please enter a valid number greater than or equal to 0');
                return;
            }
            
            try {
                const response = await fetch('/dynamic/dynamic_pricing/public/seller/inventory/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        stock_quantity: quantity
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Update the displayed stock quantity
                    const row = this.closest('tr');
                    const stockCell = row.querySelector('td:nth-child(3)');
                    stockCell.textContent = quantity;
                    
                    // Update the status badge
                    const statusCell = row.querySelector('td:nth-child(5)');
                    const minStock = parseInt(row.querySelector('td:nth-child(4)').textContent);
                    const badge = statusCell.querySelector('.badge');
                    
                    if (quantity > minStock) {
                        badge.className = 'badge bg-success';
                        badge.textContent = 'In Stock';
                    } else {
                        badge.className = 'badge bg-danger';
                        badge.textContent = 'Low Stock';
                    }
                    
                    // Update the data attribute
                    this.dataset.currentStock = quantity;
                    
                    alert('Stock updated successfully');
                } else {
                    throw new Error(result.error || 'Failed to update stock');
                }
            } catch (error) {
                console.error('Error updating stock:', error);
                alert(error.message || 'Failed to update stock');
            }
        });
    });
});