<?php
// models/Notification.php

require_once __DIR__ . '/../core/Model.php';

class Notification extends Model {
    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';
    
    public function getByUserId($userId) {
        return $this->findAll(['user_id' => $userId], 'created_at DESC');
    }
    
    public function createNotification($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['is_read'] = 0;
        return $this->create($data);
    }
    
    public function markAsRead($notificationId) {
        return $this->update($notificationId, ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')]);
    }
}
?>
