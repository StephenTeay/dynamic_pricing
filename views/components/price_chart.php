<div class="chart-container">
    <h3>Price Trend</h3>
    <canvas id="priceChart"></canvas>
</div>

<script>
    // Placeholder for chart implementation
    // Use Chart.js or similar library
    const priceData = <?php echo json_encode($priceHistory ?? []); ?>;
    
    if (document.getElementById('priceChart')) {
        const ctx = document.getElementById('priceChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: priceData.map(d => d.changed_at),
                datasets: [{
                    label: 'Price',
                    data: priceData.map(d => d.new_price),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });
    }
</script>
