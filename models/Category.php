<?php

use core\Database;

class Category {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllCategories(){
        $this->db->query("SELECT * FROM categories ORDER BY nome ASC");
        return $this->db->resultSet();
    }

    public function getCategoryById($id){
        $this->db->query("SELECT * FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addCategory($data){
        $this->db->query("INSERT INTO categories (nome, ativo) VALUES (:nome, :ativo)");
        $this->db->bind(':nome', $data['nome']);
        $this->db->bind(':ativo', $data['ativo']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function updateCategory($data){
        $this->db->query("UPDATE categories SET nome = :nome, ativo = :ativo WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nome', $data['nome']);
        $this->db->bind(':ativo', $data['ativo']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function deleteCategory($id){
        $this->db->query("DELETE FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}
