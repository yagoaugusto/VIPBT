<?php

use core\Database;

class Product {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllProducts(){
    $this->db->query("\n            SELECT \n                p.*, \n                b.nome as brand_nome, \n                c.nome as category_nome,\n                COALESCE((SELECT pp.preco FROM product_prices pp WHERE pp.product_id = p.id ORDER BY pp.vigente_desde DESC LIMIT 1), 0) as preco\n            FROM products p\n            LEFT JOIN brands b ON p.brand_id = b.id\n            LEFT JOIN categories c ON p.category_id = c.id\n            ORDER BY p.nome ASC\n        ");
        return $this->db->resultSet();
    }

    public function getProductById($id){
    $this->db->query("\n            SELECT products.* FROM products WHERE products.id = :id\n        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addProduct($data){
        $this->db->query("INSERT INTO products (sku, nome, brand_id, category_id, tipo_condicao, ativo) VALUES (:sku, :nome, :brand_id, :category_id, :tipo_condicao, :ativo)");
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':nome', $data['nome']);
        $this->db->bind(':brand_id', $data['brand_id']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':tipo_condicao', $data['tipo_condicao']);
        $this->db->bind(':ativo', $data['ativo']);
    return $this->db->execute();
    }

    public function updateProduct($data){
        $this->db->query("UPDATE products SET sku = :sku, nome = :nome, brand_id = :brand_id, category_id = :category_id, tipo_condicao = :tipo_condicao, ativo = :ativo WHERE id = :id");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':nome', $data['nome']);
        $this->db->bind(':brand_id', $data['brand_id']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':tipo_condicao', $data['tipo_condicao']);
        $this->db->bind(':ativo', $data['ativo']);
    return $this->db->execute();
    }

    public function deleteProduct($id){
        $this->db->query("DELETE FROM products WHERE id = :id");
        $this->db->bind(':id', $id);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}
