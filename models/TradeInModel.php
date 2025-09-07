<?php

use core\Database;

class TradeInModel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllTradeIns(){
        $this->db->query("
            SELECT 
                ti.*,
                c.nome as customer_nome,
                u.nome as avaliador_nome
            FROM trade_ins ti
            JOIN customers c ON ti.customer_id = c.id
            LEFT JOIN users u ON ti.avaliador_user_id = u.id
            ORDER BY ti.created_at DESC
        ");
        return $this->db->resultSet();
    }

    public function getTradeInById($id){
        $this->db->query("
            SELECT 
                ti.*,
                c.nome as customer_nome,
                u.nome as avaliador_nome
            FROM trade_ins ti
            JOIN customers c ON ti.customer_id = c.id
            LEFT JOIN users u ON ti.avaliador_user_id = u.id
            WHERE ti.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getTradeInItems($trade_in_id){
        $this->db->query("
            SELECT 
                tii.*,
                b.nome as brand_nome,
                p.nome as product_nome
            FROM trade_in_items tii
            LEFT JOIN brands b ON tii.brand_id = b.id
            LEFT JOIN products p ON tii.product_model_id = p.id
            WHERE tii.trade_in_id = :trade_in_id
        ");
        $this->db->bind(':trade_in_id', $trade_in_id);
        return $this->db->resultSet();
    }

    public function addTradeIn($data){
        $this->db->beginTransaction();
        try {
            // Insere o trade-in
            $this->db->query("INSERT INTO trade_ins (customer_id, status, avaliador_user_id) VALUES (:customer_id, :status, :avaliador_user_id)");
            $this->db->bind(':customer_id', $data['customer_id']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':avaliador_user_id', $data['avaliador_user_id']);
            $this->db->execute();
            $tradeInId = $this->db->lastInsertId();

            // Insere os itens do trade-in
            if (is_array($data['items'])) { // Adicionado o check aqui
                foreach($data['items'] as $item){
                    $this->db->query("INSERT INTO trade_in_items (trade_in_id, brand_id, product_model_id, modelo_texto, grade, serie, avaliacao_valor, valor_creditado, observacoes) VALUES (:trade_in_id, :brand_id, :product_model_id, :modelo_texto, :grade, :serie, :avaliacao_valor, :valor_creditado, :observacoes)");
                    $this->db->bind(':trade_in_id', $tradeInId);
                    $this->db->bind(':brand_id', $item['brand_id']);
                    $this->db->bind(':product_model_id', $item['product_model_id']);
                    $this->db->bind(':modelo_texto', $item['modelo_texto']);
                    $this->db->bind(':grade', $item['grade']);
                    $this->db->bind(':serie', $item['serie']);
                    $this->db->bind(':avaliacao_valor', $item['avaliacao_valor']);
                    $this->db->bind(':valor_creditado', $item['valor_creditado']);
                    $this->db->bind(':observacoes', $item['observacoes']);
                    $this->db->execute();
                }
            }

            $this->db->commit();
            return $tradeInId;
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function updateTradeInStatus($trade_in_id, $status, $stock_item_resultante_id = null){
        $this->db->beginTransaction();
        try {
            $this->db->query("UPDATE trade_ins SET status = :status WHERE id = :id");
            $this->db->bind(':status', $status);
            $this->db->bind(':id', $trade_in_id);
            $this->db->execute();

            // Se o status for 'aprovado' e houver um stock_item_resultante_id, atualiza o item de trade-in
            if($status == 'aprovado' && $stock_item_resultante_id){
                $this->db->query("UPDATE trade_in_items SET stock_item_resultante_id = :stock_item_resultante_id WHERE trade_in_id = :trade_in_id");
                $this->db->bind(':stock_item_resultante_id', $stock_item_resultante_id);
                $this->db->bind(':trade_in_id', $trade_in_id);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function addOrderCredit($order_id, $trade_in_id, $valor){
        $this->db->query("INSERT INTO order_credits (order_id, origem, descricao, valor) VALUES (:order_id, 'trade_in', 'Crédito de Trade-in #{$trade_in_id}', :valor)");
        $this->db->bind(':order_id', $order_id);
        $this->db->bind(':trade_in_id', $trade_in_id); // This is not used in the query, but in the description.
        $this->db->bind(':valor', $valor);
        return $this->db->execute();
    }
}
