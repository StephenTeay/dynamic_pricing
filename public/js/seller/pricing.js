// Pricing Management JS

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.update-price').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const productId = btn.getAttribute('data-product-id');
      const newPrice = prompt('Enter new price:');
      if (newPrice !== null && !isNaN(newPrice)) {
        fetch('/seller/pricing/update', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ product_id: productId, price: newPrice })
        })
        .then(res => res.json())
        .then(data => {
          alert(data.success ? 'Price updated!' : 'Error: ' + data.error);
          if (data.success) location.reload();
        });
      }
    });
  });
});
