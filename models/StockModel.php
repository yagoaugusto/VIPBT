<?php

use core\Database;

class StockModel {
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

    public function addStockMovement($data){
        $this->db->beginTransaction();
        try {
            // Insere a movimentação de entrada
            $this->db->query("INSERT INTO inventory_moves (product_id, tipo, qtd, ref_origem, observacao) VALUES (:product_id, 'entrada', :qtd, :ref_origem, :observacao)");
            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':qtd', $data['qtd']);
            $this->db->bind(':ref_origem', 'Entrada Manual');
            $this->db->bind(':observacao', $data['observacao']);
            $this->db->execute();
            $moveId = $this->db->lastInsertId();

            // Para produtos novos, assumimos que cada entrada gera um item de estoque.
            // A lógica pode ser mais complexa (ex: um único stock_item com quantidade),
            // mas para o controle unitário de seminovos, este modelo é mais flexível.
            $this->db->query("SELECT tipo_condicao FROM products WHERE id = :product_id");
            $this->db->bind(':product_id', $data['product_id']);
            $product = $this->db->single();

            if($product->tipo_condicao == 'novo'){
                for($i = 0; $i < $data['qtd']; $i++){
                    $this->db->query("INSERT INTO stock_items (product_id, condicao, aquisicao_tipo, aquisicao_custo) VALUES (:product_id, 'novo', 'compra', :custo)");
                    $this->db->bind(':product_id', $data['product_id']);
                    $this->db->bind(':custo', $data['custo']);
                    $this->db->execute();
                }
            }
            
            $this->db->commit();
            return true;

        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function getAvailableStockItemsForLoan(){
        $this->db->query(" 
            SELECT 
                si.id,
                si.product_id,
                si.serie,
                p.nome as product_nome,
                p.sku,
                b.nome as brand_nome
            FROM stock_items si
            JOIN products p ON si.product_id = p.id
            JOIN brands b ON p.brand_id = b.id
            WHERE si.status = 'em_estoque' AND p.tipo_condicao = 'novo' -- Apenas itens novos em estoque
            ORDER BY p.nome ASC, si.serie ASC
        ");
        return $this->db->resultSet();
    }
}
