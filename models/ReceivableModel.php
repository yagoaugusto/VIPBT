<?php

use core\Database;

class ReceivableModel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllReceivables(){
        $this->db->query("
            SELECT 
                r.*,
                o.public_code,
                c.nome as customer_nome
            FROM receivables r
            JOIN orders o ON r.order_id = o.id
            JOIN customers c ON o.customer_id = c.id
            ORDER BY r.atualizado_em DESC
        ");
        return $this->db->resultSet();
    }

    public function getReceivableByOrderId($order_id){
        $this->db->query("SELECT * FROM receivables WHERE order_id = :order_id");
        $this->db->bind(':order_id', $order_id);
        return $this->db->single();
    }
}
