<?php

use core\Database;

class PaymentModel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function addPayment($data){
        $this->db->beginTransaction();
        try {
            // Insere o pagamento
            $this->db->query("INSERT INTO payments (order_id, forma, valor, data, status_pagamento) VALUES (:order_id, :forma, :valor, :data, :status_pagamento)");
            $this->db->bind(':order_id', $data['order_id']);
            $this->db->bind(':forma', $data['forma']);
            $this->db->bind(':valor', $data['valor']);
            $this->db->bind(':data', $data['data']);
            $this->db->bind(':status_pagamento', $data['status_pagamento']);
            $this->db->execute();

            // Atualiza o contas a receber
            $this->db->query("SELECT valor_total, valor_recebido FROM receivables WHERE order_id = :order_id");
            $this->db->bind(':order_id', $data['order_id']);
            $receivable = $this->db->single();

            $newValorRecebido = $receivable->valor_recebido + $data['valor'];
            $newValorAReceber = $receivable->valor_total - $newValorRecebido;

            $this->db->query("UPDATE receivables SET valor_recebido = :valor_recebido, valor_a_receber = :valor_a_receber WHERE order_id = :order_id");
            $this->db->bind(':valor_recebido', $newValorRecebido);
            $this->db->bind(':valor_a_receber', $newValorAReceber);
            $this->db->bind(':order_id', $data['order_id']);
            $this->db->execute();

            // Registra a entrada no caixa
            $this->db->query("INSERT INTO cash_entries (origem, id_origem, forma, valor, data) VALUES ('pagamento', :id_origem, :forma, :valor, :data)");
            $this->db->bind(':id_origem', $data['order_id']);
            $this->db->bind(':forma', $data['forma']);
            $this->db->bind(':valor', $data['valor']);
            $this->db->bind(':data', $data['data']);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function getPaymentsByOrderId($order_id){
        $this->db->query("SELECT * FROM payments WHERE order_id = :order_id ORDER BY data ASC");
        $this->db->bind(':order_id', $order_id);
        return $this->db->resultSet();
    }
}
