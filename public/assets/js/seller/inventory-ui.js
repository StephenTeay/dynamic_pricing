// public/assets/js/seller/inventory-ui.js

(function() {
    'use strict';

    // Stock Update Manager
    const InventoryManager = {
        modal: null,
        form: null,
        baseUrl: window.BASE_URL || '',

        init: function() {
            this.modal = document.getElementById('stock-update-modal');
            this.form = document.getElementById('stock-update-form');
            
            if (!this.modal || !this.form) {
                console.error('Inventory UI elements not found');
                return;
            }

            this.attachEventListeners();
            console.log('Inventory Manager initialized');
        },

        attachEventListeners: function() {
            // Handle form submission
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleStockUpdate();
            });

            // Handle update stock buttons
            const updateButtons = document.querySelectorAll('.update-stock');
            updateButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    this.openModal(e.target);
                });
            });

            // Close modal on background click
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) {
                    this.closeModal();
                }
            });

            // Close modal on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.modal.classList.contains('active')) {
                    this.closeModal();
                }
            });

            // Update quantity preview on operation change
            const operationSelect = document.getElementById('stock-operation');
            const quantityInput = document.getElementById('stock-quantity');
            
            if (operationSelect && quantityInput) {
                operationSelect.addEventListener('change', () => {
                    this.updateQuantityPreview();
                });
                
                quantityInput.addEventListener('input', () => {
                    this.updateQuantityPreview();
                });
            }
        },

        openModal: function(button) {
            const productId = button.dataset.productId;
            const productName = button.dataset.productName;
            const currentStock = parseInt(button.dataset.currentStock) || 0;

            // Populate modal
            document.getElementById('modal-product-id').value = productId;
            document.getElementById('modal-product-name').textContent = productName;
            document.getElementById('modal-current-stock').textContent = currentStock;

            // Reset form
            this.form.reset();
            document.getElementById('stock-operation').value = 'add';
            document.getElementById('stock-quantity').value = '';

            // Show modal
            this.modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scroll

            // Focus on quantity input
            setTimeout(() => {
                document.getElementById('stock-quantity').focus();
            }, 100);
        },

        closeModal: function() {
            this.modal.classList.remove('active');
            document.body.style.overflow = ''; // Restore scroll
            this.form.reset();
        },

        updateQuantityPreview: function() {
            const operation = document.getElementById('stock-operation').value;
            const quantity = parseInt(document.getElementById('stock-quantity').value) || 0;
            const currentStock = parseInt(document.getElementById('modal-current-stock').textContent) || 0;
            
            let newStock = currentStock;
            
            switch(operation) {
                case 'add':
                    newStock = currentStock + quantity;
                    break;
                case 'set':
                    newStock = quantity;
                    break;
                case 'remove':
                    newStock = Math.max(0, currentStock - quantity);
                    break;
            }

            // Show preview (you can add a preview element if desired)
            console.log('Preview:', { operation, currentStock, quantity, newStock });
        },

        async handleStockUpdate() {
            const productId = document.getElementById('modal-product-id').value;
            const operation = document.getElementById('stock-operation').value;
            const quantity = parseInt(document.getElementById('stock-quantity').value);
            const reason = document.getElementById('stock-reason').value;
            const currentStock = parseInt(document.getElementById('modal-current-stock').textContent) || 0;

            if (!quantity || quantity < 0) {
                this.showToast('Please enter a valid quantity', 'error');
                return;
            }

            // Calculate new stock based on operation
            let newStock = currentStock;
            switch(operation) {
                case 'add':
                    newStock = currentStock + quantity;
                    break;
                case 'set':
                    newStock = quantity;
                    break;
                case 'remove':
                    newStock = Math.max(0, currentStock - quantity);
                    break;
            }

            // Show loading state
            const submitButton = this.form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<span>Updating...</span>';
            submitButton.disabled = true;

            try {
                // Make API call to update stock
                const response = await fetch(`${this.baseUrl}/api/v1/inventory/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        operation: operation,
                        quantity: quantity,
                        new_stock: newStock,
                        reason: reason || null
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showToast('Stock updated successfully!', 'success');
                    this.closeModal();
                    
                    // Reload page to show updated stock
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(result.message || 'Failed to update stock');
                }
            } catch (error) {
                console.error('Error updating stock:', error);
                this.showToast(error.message || 'Failed to update stock. Please try again.', 'error');
                
                // Restore button
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        },

        showToast: function(message, type = 'info') {
            // Remove existing toasts
            const existingToasts = document.querySelectorAll('.toast');
            existingToasts.forEach(toast => toast.remove());

            // Create toast
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            
            document.body.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.3s ease-out forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        },

        // Export stock data to CSV
        exportToCSV: function() {
            const table = document.querySelector('.table');
            if (!table) return;

            let csv = [];
            const rows = table.querySelectorAll('tr');

            rows.forEach(row => {
                const cols = row.querySelectorAll('td, th');
                const rowData = [];
                
                cols.forEach((col, index) => {
                    // Skip the actions column
                    if (index < cols.length - 1) {
                        rowData.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
                    }
                });
                
                csv.push(rowData.join(','));
            });

            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            
            a.setAttribute('hidden', '');
            a.setAttribute('href', url);
            a.setAttribute('download', `inventory_${new Date().toISOString().split('T')[0]}.csv`);
            
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            this.showToast('Inventory exported successfully!', 'success');
        },

        // Print inventory report
        printInventory: function() {
            window.print();
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            InventoryManager.init();
        });
    } else {
        InventoryManager.init();
    }

    // Make functions available globally
    window.closeStockModal = function() {
        InventoryManager.closeModal();
    };

    window.exportInventory = function() {
        InventoryManager.exportToCSV();
    };

    window.printInventory = function() {
        InventoryManager.printInventory();
    };

})();