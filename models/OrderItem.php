<?php
// models/OrderItem.php

require_once __DIR__ . '/../core/Model.php';

class OrderItem extends Model {
    protected $table = 'order_items';
    protected $primaryKey = 'order_item_id';
    
    public function getByOrderId($orderId) {
        return $this->findAll(['order_id' => $orderId]);
    }
    
    public function createItem($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
}
?>
