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

    // Atualiza a data de cobrança (aceita null para limpar)
    public function updateChargeDate($order_id, $date_or_null){
        // Normaliza valor: strings vazias viram null
        $value = ($date_or_null === '' || $date_or_null === false) ? null : $date_or_null;

        // Atualiza coluna e deixa o TIMESTAMP de atualizado_em ser gerenciado pelo MySQL (ON UPDATE CURRENT_TIMESTAMP)
        $this->db->query("UPDATE receivables SET data_cobranca = :data_cobranca WHERE order_id = :order_id");
        $this->db->bind(':order_id', $order_id);
        $this->db->bind(':data_cobranca', $value);
        $this->db->execute();

        // Retorna dados atuais para refletir na UI
        $this->db->query("SELECT data_cobranca, atualizado_em FROM receivables WHERE order_id = :order_id");
        $this->db->bind(':order_id', $order_id);
        return $this->db->single();
    }
}
