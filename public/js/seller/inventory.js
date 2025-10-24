// Inventory Management JS

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.update-stock').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const productId = btn.getAttribute('data-product-id');
      const newStock = prompt('Enter new stock quantity:');
      if (newStock !== null && !isNaN(newStock)) {
        fetch('/seller/inventory/update', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ product_id: productId, stock_quantity: newStock })
        })
        .then(res => res.json())
        .then(data => {
          alert(data.success ? 'Stock updated!' : 'Error: ' + data.error);
          if (data.success) location.reload();
        });
      }
    });
  });
});
