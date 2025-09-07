<?php

use core\Database;

class FulfillmentModel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getFulfillmentsByOrderId($order_id){
        $this->db->query("SELECT * FROM fulfillments WHERE order_id = :order_id ORDER BY created_at DESC");
        $this->db->bind(':order_id', $order_id);
        return $this->db->resultSet();
    }

    public function addFulfillment($data){
        $this->db->beginTransaction();
        try {
            // Insere o registro de fulfillment
            $this->db->query("INSERT INTO fulfillments (order_id, status, transportadora, codigo_rastreio, enviado_em, entregue_em, observacoes) VALUES (:order_id, :status, :transportadora, :codigo_rastreio, :enviado_em, :entregue_em, :observacoes)");
            $this->db->bind(':order_id', $data['order_id']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':transportadora', $data['transportadora']);
            $this->db->bind(':codigo_rastreio', $data['codigo_rastreio']);
            $this->db->bind(':enviado_em', $data['enviado_em']);
            $this->db->bind(':entregue_em', $data['entregue_em']);
            $this->db->bind(':observacoes', $data['observacoes']);
            $this->db->execute();

            // Atualiza o status de entrega do pedido
            $this->db->query("UPDATE orders SET status_entrega = :status_entrega WHERE id = :order_id");
            $this->db->bind(':status_entrega', $data['status']);
            $this->db->bind(':order_id', $data['order_id']);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
}
