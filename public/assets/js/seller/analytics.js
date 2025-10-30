class AnalyticsView {
    static async loadAnalytics() {
        try {
            const analytics = await App.get('/analytics/revenue');
            this.renderCharts(analytics);
        } catch (error) {
            console.error('Failed to load analytics:', error);
        }
    }
    
    static renderCharts(data) {
        // Charts will be rendered here
        console.log('Analytics data:', data);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    AnalyticsView.loadAnalytics();
});