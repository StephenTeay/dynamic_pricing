<?php
// cron/update_exchange_rates.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/ExchangeRateService.php';

Logger::info('Starting exchange rate update cron job');

try {
    $exchangeRateService = new ExchangeRateService();
    
    // Update exchange rates for supported currencies
    $baseCurrency = DEFAULT_CURRENCY;
    
    foreach (SUPPORTED_CURRENCIES as $targetCurrency) {
        if ($baseCurrency === $targetCurrency) {
            continue;
        }
        
        // In production, fetch from external API (e.g., Open Exchange Rates, Fixer.io)
        // For now, using mock data
        $rate = $this->getMockRate($baseCurrency, $targetCurrency);
        
        $exchangeRateService->updateRate($baseCurrency, $targetCurrency, $rate);
    }
    
    Logger::info('Exchange rate update completed');
    
} catch (Exception $e) {
    Logger::error('Exchange rate update failed: ' . $e->getMessage());
}

/**
 * Get mock exchange rate
 */
function getMockRate($from, $to) {
    $rates = [
        'NGN_USD' => 1200,
        'NGN_EUR' => 1300,
        'NGN_GBP' => 1500,
        'USD_NGN' => 0.00083,
        'EUR_NGN' => 0.00077,
        'GBP_NGN' => 0.00067
    ];
    
    $key = "{$from}_{$to}";
    return $rates[$key] ?? 1.0;
}
?>
