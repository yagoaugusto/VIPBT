<?php

use core\Database;

class CommissionModel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllCommissions(){
        $this->db->query("
            SELECT 
                com.*,
                o.public_code,
                u.nome as seller_nome
            FROM commissions com
            JOIN orders o ON com.order_id = o.id
            JOIN sellers s ON com.seller_id = s.id
            JOIN users u ON s.user_id = u.id
            ORDER BY com.created_at DESC
        ");
        return $this->db->resultSet();
    }

    public function getCommissionsBySellerId($seller_id){
        $this->db->query("
            SELECT 
                com.*,
                o.public_code
            FROM commissions com
            JOIN orders o ON com.order_id = o.id
            WHERE com.seller_id = :seller_id
            ORDER BY com.created_at DESC
        ");
        $this->db->bind(':seller_id', $seller_id);
        return $this->db->resultSet();
    }

    public function addCommission($data){
        $this->db->query("INSERT INTO commissions (order_id, seller_id, base_calculo, perc, valor, status) VALUES (:order_id, :seller_id, :base_calculo, :perc, :valor, :status)");
        $this->db->bind(':order_id', $data['order_id']);
        $this->db->bind(':seller_id', $data['seller_id']);
        $this->db->bind(':base_calculo', $data['base_calculo']);
        $this->db->bind(':perc', $data['perc']);
        $this->db->bind(':valor', $data['valor']);
        $this->db->bind(':status', $data['status']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}
