<?php
// models/ExchangeRate.php

require_once __DIR__ . '/../core/Model.php';

class ExchangeRate extends Model {
    protected $table = 'exchange_rates';
    protected $primaryKey = 'rate_id';
    
    public function getLatestRate($fromCurrency, $toCurrency) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE from_currency = :from AND to_currency = :to
                  ORDER BY last_updated DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':from' => $fromCurrency, ':to' => $toCurrency]);
        return $stmt->fetch();
    }
    
    public function updateRate($fromCurrency, $toCurrency, $rate) {
        $existing = $this->getLatestRate($fromCurrency, $toCurrency);
        
        if ($existing) {
            return $this->update($existing['rate_id'], [
                'rate' => $rate
            ]);
        } else {
            return $this->create([
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'rate' => $rate,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
?>
