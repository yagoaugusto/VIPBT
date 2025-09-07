<?php

use core\Database;

class Product {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllProducts(){
        $this->db->query("
            SELECT 
                products.*, 
                brands.nome as brand_nome, 
                categories.nome as category_nome,
                (SELECT preco FROM product_prices pp WHERE pp.product_id = products.id ORDER BY pp.vigente_desde DESC LIMIT 1) as preco
            FROM products
            JOIN brands ON products.brand_id = brands.id
            JOIN categories ON products.category_id = categories.id
            ORDER BY products.nome ASC
        ");
        return $this->db->resultSet();
    }

    public function getProductById($id){
        $this->db->query("
            SELECT 
                products.*,
                pp.preco,
                pp.custo
            FROM products 
            LEFT JOIN product_prices pp ON pp.product_id = products.id AND pp.vigente_desde = (SELECT MAX(vigente_desde) FROM product_prices WHERE product_id = products.id)
            WHERE products.id = :id
        ");
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

        if($this->db->execute()){
            $productId = $this->db->lastInsertId();
            $this->db->query("INSERT INTO product_prices (product_id, custo, preco, vigente_desde) VALUES (:product_id, :custo, :preco, :vigente_desde)");
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':custo', $data['custo']);
            $this->db->bind(':preco', $data['preco']);
            $this->db->bind(':vigente_desde', date('Y-m-d'));
            return $this->db->execute();
        } else {
            return false;
        }
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

        if($this->db->execute()){
            $this->db->query("SELECT preco, custo FROM product_prices WHERE product_id = :id ORDER BY vigente_desde DESC LIMIT 1");
            $this->db->bind(':id', $data['id']);
            $lastPrice = $this->db->single();

            if(!$lastPrice || $lastPrice->preco != $data['preco'] || $lastPrice->custo != $data['custo']) {
                $this->db->query("INSERT INTO product_prices (product_id, custo, preco, vigente_desde) VALUES (:product_id, :custo, :preco, :vigente_desde)");
                $this->db->bind(':product_id', $data['id']);
                $this->db->bind(':custo', $data['custo']);
                $this->db->bind(':preco', $data['preco']);
                $this->db->bind(':vigente_desde', date('Y-m-d'));
                return $this->db->execute();
            }
            return true;
        } else {
            return false;
        }
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
