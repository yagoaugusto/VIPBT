<?php

use core\Database;

class Brand {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Retorna todas as marcas
    public function getAllBrands(){
        $this->db->query("SELECT * FROM brands ORDER BY nome ASC");
        return $this->db->resultSet();
    }

    // Retorna uma marca pelo ID
    public function getBrandById($id){
        $this->db->query("SELECT * FROM brands WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Adiciona uma nova marca
    public function addBrand($data){
        $this->db->query("INSERT INTO brands (nome, ativo) VALUES (:nome, :ativo)");
        $this->db->bind(':nome', $data['nome']);
        $this->db->bind(':ativo', $data['ativo']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    // Atualiza uma marca
    public function updateBrand($data){
        $this->db->query("UPDATE brands SET nome = :nome, ativo = :ativo WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nome', $data['nome']);
        $this->db->bind(':ativo', $data['ativo']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    // Deleta uma marca
    public function deleteBrand($id){
        $this->db->query("DELETE FROM brands WHERE id = :id");
        $this->db->bind(':id', $id);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}
