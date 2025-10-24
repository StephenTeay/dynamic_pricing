<?php
// services/ExchangeRateService.php

require_once __DIR__ . '/../models/ExchangeRate.php';
require_once __DIR__ . '/../utils/logger.php';

class ExchangeRateService {
    private $exchangeRateModel;
    private $apiKey; // Your currency API key
    
    public function __construct() {
        $this->exchangeRateModel = new ExchangeRate();
        $this->apiKey = getenv('CURRENCY_API_KEY') ?: null;
    }
    
    /**
     * Get latest exchange rate from DB or API
     */
    public function getExchangeRate($fromCurrency, $toCurrency) {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }
        
        try {
            // Try to get rate from database first
            $rate = $this->exchangeRateModel->getLatestRate($fromCurrency, $toCurrency);
            
            // If rate is older than 1 hour or doesn't exist, fetch from API
            if (!$rate || (time() - strtotime($rate['last_updated']) > 3600)) {
                $newRate = $this->fetchRealTimeRate($fromCurrency, $toCurrency);
                if ($newRate) {
                    $this->updateRate($fromCurrency, $toCurrency, $newRate);
                    return $newRate;
                }
            }
            
            return $rate ? $rate['rate'] : null;
            
        } catch (Exception $e) {
            Logger::error("Exchange rate error: " . $e->getMessage());
            // Return last known rate if available
            $lastRate = $this->exchangeRateModel->getLatestRate($fromCurrency, $toCurrency);
            return $lastRate ? $lastRate['rate'] : null;
        }
    }
    
    /**
     * Fetch real-time exchange rate from API
     */
    private function fetchRealTimeRate($fromCurrency, $toCurrency) {
        if (!$this->apiKey) {
            return null;
        }
        
        try {
            // Example using a currency API (you'll need to replace with your preferred API)
            $url = "https://api.exchangerate-api.com/v4/latest/{$fromCurrency}";
            
            $response = file_get_contents($url);
            if ($response === false) {
                throw new Exception("Failed to fetch exchange rates");
            }
            
            $data = json_decode($response, true);
            if (isset($data['rates'][$toCurrency])) {
                return $data['rates'][$toCurrency];
            }
            
            return null;
            
        } catch (Exception $e) {
            Logger::error("API fetch error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Convert amount between currencies
     */
    public function convert($amount, $fromCurrency, $toCurrency) {
        $rate = $this->getExchangeRate($fromCurrency, $toCurrency);
        
        if (!$rate) {
            throw new Exception("No exchange rate available for {$fromCurrency} to {$toCurrency}");
        }
        
        return round($amount * $rate, 2);
    }
    
    /**
     * Update exchange rate in database
     */
    public function updateRate($fromCurrency, $toCurrency, $rate) {
        try {
            return $this->exchangeRateModel->updateRate($fromCurrency, $toCurrency, $rate);
        } catch (Exception $e) {
            Logger::error("Failed to update exchange rate: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update all exchange rates
     */
    public function updateAllRates() {
        $currencies = ['NGN', 'USD', 'EUR', 'GBP']; // Add more as needed
        
        foreach ($currencies as $from) {
            foreach ($currencies as $to) {
                if ($from !== $to) {
                    $rate = $this->fetchRealTimeRate($from, $to);
                    if ($rate) {
                        $this->updateRate($from, $to, $rate);
                    }
                }
            }
        }
    }
}
