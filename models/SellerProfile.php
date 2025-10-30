<?php
// models/SellerProfile.php

require_once __DIR__ . '/../core/Model.php';

class SellerProfile extends Model {
    protected $table = 'seller_profiles';
    protected $primaryKey = 'seller_id';
    
    public function getByUserId($userId) {
        return $this->findOne(['user_id' => $userId]);
    }
}
?>
