<?php

use core\Database;

class Stock {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllStockItems(){
        $this->db->query("
            SELECT 
                si.*,
                p.nome as product_nome,
                p.sku,
                b.nome as brand_nome
            FROM stock_items si
            JOIN products p ON si.product_id = p.id
            JOIN brands b ON p.brand_id = b.id
            ORDER BY p.nome ASC, si.id ASC
        ");
        return $this->db->resultSet();
    }
}
