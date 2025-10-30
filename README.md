# Dynamic Pricing System

A sophisticated PHP-based dynamic pricing system that automatically adjusts product prices based on multiple factors including inventory levels, demand, time-based patterns, and custom seller rules.

## Features

- **Dynamic Price Adjustments** based on:
  - Inventory levels (low/high stock triggers)
  - Time-based factors (peak hours, weekends)
  - Demand patterns
  - Seller-defined rules
  - Market competition

- **Inventory Management**
  - Low stock alerts
  - Automatic price increases for scarce items
  - High stock price optimization
  - Stock threshold customization

- **Seller Tools**
  - Custom pricing rules
  - Analytics dashboard
  - Sales performance metrics
  - Inventory alerts
  - Price change notifications

- **Buyer Features**
  - Real-time price updates
  - Currency conversion
  - Product availability tracking
  - Order management

## System Architecture

### Core Components
- `core/` - Framework core components
  - Database connection management
  - Model base class
  - Response handling
  - Routing system
  - Session management
  - Input validation

### API Endpoints
- `api/v1/` - RESTful API endpoints
  - Authentication
  - Products
  - Pricing
  - Orders
  - Inventory
  - Analytics

### Business Logic
- `controllers/` - Business logic handlers
- `models/` - Data models and database interactions
- `services/` - Core business services
  - PricingEngine
  - AnalyticsService
  - NotificationService
  - ExchangeRateService

### Frontend
- `public/` - Public assets and entry points
- `views/` - PHP view templates
  - Buyer interface
  - Seller dashboard
  - Admin panels

## Technology Stack

- **Backend**
  - PHP 8.2+
  - MySQL/MariaDB
  - RESTful API

- **Frontend**
  - HTML5
  - CSS3
  - JavaScript
  - Bootstrap

- **Development Tools**
  - Composer for dependency management
  - PHPUnit for testing
  - Git for version control

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/StephenTeay/dynamic_pricing_new.git
   ```

2. Configure your web server (Apache/Nginx) to point to the `public/` directory

3. Create and configure the database:
   ```sql
   CREATE DATABASE dynamic_pricing_db;
   username = root
   password = 
   ```
   Import the dynamic_pricing_db.sql




## Configuration

### Database Setup
Configure database connection in `config/database.php`:
```php
return [
    'host' => 'localhost',
    'database' => 'dynamic_pricing_db',
    'username' => 'root',
    'password' => ''
];
```



## Usage

### Price Update Triggers

The system automatically adjusts prices based on:

1. **Inventory Changes**
   - Below low_stock_threshold: Price increases up to 20%
   - Above high_stock_threshold: Price decreases up to 15%
   - Gradual adjustments between thresholds

2. **Time-Based Adjustments**
   - Peak hours (12-2pm, 6-8pm): +5%
   - Weekend pricing: +10%

3. **Demand-Based Pricing**
   - High demand (>10 orders/24h): +15%
   - Medium demand (5-10 orders/24h): +8%
   - Low demand (<1 order/24h): -5%

### Pricing Rules
Custom rules can be set up in the seller dashboard:
```php
[
    'rule_type' => 'inventory_based',
    'min_value' => 1000,
    'max_value' => 5000,
    'percentage_change' => 10
]
```

## Testing

Run the test suite:
```bash
php test_pricing.php
php test_inventory_price_update.php
```

## Cron Jobs

Set up the following cron jobs:

```bash
# Update exchange rates every hour
0 * * * * php /path/to/cron/update_exchange_rates.php

# Generate analytics daily
0 0 * * * php /path/to/cron/generate_analytics.php

# Check inventory and update prices every 15 minutes
*/15 * * * * php /path/to/cron/update_prices.php
```

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

Preferably, use XAMPP sever.
If you need any help, you can reach out to me.
