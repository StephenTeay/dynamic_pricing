<?php
// core/Model.php

require_once __DIR__ . '/../config/database.php';

class Model {
    protected $db;
    protected $table = '';
    protected $primaryKey = 'id';
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function find($id) {
        $query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function findOne($conditions = []) {
        $where = $this->buildWhere($conditions);
        $query = "SELECT * FROM {$this->table} $where LIMIT 1";
        $stmt = $this->db->prepare($query);
        $this->bindValues($stmt, $conditions);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function findAll($conditions = [], $orderBy = '', $limit = null, $offset = 0) {
        $where = $this->buildWhere($conditions);
        $query = "SELECT * FROM {$this->table} $where";
        
        if ($orderBy) {
            $query .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $query .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $this->db->prepare($query);
        $this->bindValues($stmt, $conditions);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        
        $query = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") 
                  VALUES (" . implode(',', $placeholders) . ")";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    public function update($id, $data) {
        $sets = array_map(fn($k) => "$k = :$k", array_keys($data));
        $query = "UPDATE {$this->table} SET " . implode(',', $sets) . 
                 " WHERE {$this->primaryKey} = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
    
    public function count($conditions = []) {
        $where = $this->buildWhere($conditions);
        $query = "SELECT COUNT(*) as count FROM {$this->table} $where";
        $stmt = $this->db->prepare($query);
        $this->bindValues($stmt, $conditions);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    public function beginTransaction() {
        $this->db->beginTransaction();
    }
    
    public function commit() {
        $this->db->commit();
    }
    
    public function rollback() {
        $this->db->rollBack();
    }
    
    protected function buildWhere($conditions) {
        if (empty($conditions)) {
            return '';
        }
        
        $wheres = array_map(fn($k) => "$k = :$k", array_keys($conditions));
        return 'WHERE ' . implode(' AND ', $wheres);
    }
    
    protected function bindValues($stmt, $conditions) {
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
    }
}
?>
