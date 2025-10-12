<?php
// services/ExchangeRateService.php

require_once __DIR__ . '/../models/ExchangeRate.php';

class ExchangeRateService {
    private $exchangeRateModel;
    
    public function __construct() {
        $this->exchangeRateModel = new ExchangeRate();
    }
    
    /**
     * Get exchange rate
     */
    public function getRate($fromCurrency, $toCurrency) {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }
        
        $rate = $this->exchangeRateModel->getLatestRate($fromCurrency, $toCurrency);
        
        return $rate ? $rate['rate'] : null;
    }
    
    /**
     * Convert currency
     */
    public function convert($amount, $fromCurrency, $toCurrency) {
        $rate = $this->getRate($fromCurrency, $toCurrency);
        
        if (!$rate) {
            return null;
        }
        
        return round($amount * $rate, 2);
    }
    
    /**
     * Update exchange rate
     */
    public function updateRate($fromCurrency, $toCurrency, $rate) {
        return $this->exchangeRateModel->updateRate($fromCurrency, $toCurrency, $rate);
    }
}
?>
