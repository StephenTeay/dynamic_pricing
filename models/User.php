<?php
// models/User.php

require_once __DIR__ . '/../core/Model.php';

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    
    public function createUser($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['is_active'] = 1;
        return $this->create($data);
    }
    
    public function verifyLogin($identifier, $password) {
        $query = "SELECT * FROM {$this->table} WHERE (username = :identifier OR email = :identifier) LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':identifier' => $identifier]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password']) && $user['is_active']) {
            return $user;
        }
        return false;
    }
    
    public function emailExists($email) {
        return $this->count(['email' => $email]) > 0;
    }
    
    public function usernameExists($username) {
        return $this->count(['username' => $username]) > 0;
    }
    
    public function updatePassword($userId, $password) {
        return $this->update($userId, ['password' => password_hash($password, PASSWORD_BCRYPT)]);
    }
    
    public function createPasswordResetToken($email) {
        $user = $this->findOne(['email' => $email]);
        if (!$user) return false;
        
        $token = bin2hex(random_bytes(32));
        $query = "INSERT INTO password_resets (user_id, token, expires_at) 
                  VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':user_id' => $user['user_id'], ':token' => $token]) ? $token : false;
    }
    
    public function verifyPasswordResetToken($token) {
        $query = "SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }
    
    public function markTokenUsed($token) {
        $query = "DELETE FROM password_resets WHERE token = :token";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':token' => $token]);
    }
}
?>
