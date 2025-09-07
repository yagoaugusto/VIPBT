<?php

use core\Database;

class SellerModel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllSellers(){
        $this->db->query("
            SELECT s.id, u.nome 
            FROM sellers s
            JOIN users u ON s.user_id = u.id
            WHERE u.ativo = 1 AND u.perfil IN ('vendedor', 'admin')
            ORDER BY u.nome ASC
        ");
        return $this->db->resultSet();
    }
}
