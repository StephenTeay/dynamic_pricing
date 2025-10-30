// public/assets/js/seller/pricing-ui.js

document.addEventListener('DOMContentLoaded', function() {
    // Handle price update button clicks
    document.querySelectorAll('.update-price').forEach(button => {
        button.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            const row = this.closest('tr');
            const currentPrice = parseFloat(row.querySelector('td:nth-child(2)').textContent.replace('$', '').trim());
            const currentCost = parseFloat(row.querySelector('td:nth-child(3)').textContent.replace('$', '').trim());
            
            // Show modal with current price value
            const newPrice = window.prompt('Enter new price:', currentPrice);
            
            if (newPrice === null) return; // User cancelled
            
            const price = parseFloat(newPrice);
            if (isNaN(price) || price < 0) {
                alert('Please enter a valid number greater than or equal to 0');
                return;
            }

            // Warn if price is below cost
            if (price < currentCost) {
                if (!confirm('Warning: The new price is below the product cost. Do you want to continue?')) {
                    return;
                }
            }
            
            try {
                const response = await fetch('/dynamic/dynamic_pricing/public/seller/pricing/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        price: price
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Update the displayed price
                    row.querySelector('td:nth-child(2)').textContent = '$' + price.toFixed(2);
                    
                    // Update the margin
                    const margin = currentCost > 0 ? ((price - currentCost) / price) * 100 : 0;
                    row.querySelector('td:nth-child(4)').textContent = margin.toFixed(1) + '%';
                    
                    // Update the last updated date
                    row.querySelector('td:nth-child(5)').textContent = new Date().toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    });
                    
                    alert('Price updated successfully');
                } else {
                    throw new Error(result.error || 'Failed to update price');
                }
            } catch (error) {
                console.error('Error updating price:', error);
                alert(error.message || 'Failed to update price');
            }
        });
    });
});