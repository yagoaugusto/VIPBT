<?php

use core\Database;

class Customer {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllCustomers(){
        $this->db->query("SELECT * FROM customers ORDER BY nome ASC");
        return $this->db->resultSet();
    }

    public function getCustomerById($id){
        $this->db->query("SELECT * FROM customers WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addCustomer($data){
        $this->db->query("INSERT INTO customers (nome, telefone, cidade, raquete_entrada_bool, raquete_entrada_produto_id) VALUES (:nome, :telefone, :cidade, :raquete_entrada_bool, :raquete_entrada_produto_id)");
        $this->db->bind(':nome', $data['nome']);
        $this->db->bind(':telefone', $data['telefone']);
        $this->db->bind(':cidade', $data['cidade']);
        $this->db->bind(':raquete_entrada_bool', $data['raquete_entrada_bool']);
        $this->db->bind(':raquete_entrada_produto_id', $data['raquete_entrada_produto_id']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function updateCustomer($data){
        $this->db->query("UPDATE customers SET nome = :nome, telefone = :telefone, cidade = :cidade, raquete_entrada_bool = :raquete_entrada_bool, raquete_entrada_produto_id = :raquete_entrada_produto_id WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':nome', $data['nome']);
        $this->db->bind(':telefone', $data['telefone']);
        $this->db->bind(':cidade', $data['cidade']);
        $this->db->bind(':raquete_entrada_bool', $data['raquete_entrada_bool']);
        $this->db->bind(':raquete_entrada_produto_id', $data['raquete_entrada_produto_id']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function deleteCustomer($id){
        $this->db->query("DELETE FROM customers WHERE id = :id");
        $this->db->bind(':id', $id);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}
