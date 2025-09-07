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
            // Validação dos dados
            if (empty($data['product_id']) || !is_numeric($data['product_id'])) {
                throw new Exception('ID do produto é obrigatório e deve ser numérico');
            }
            
            if (empty($data['qtd']) || !is_numeric($data['qtd']) || $data['qtd'] <= 0) {
                throw new Exception('Quantidade deve ser um número maior que zero');
            }
            
            if (!isset($data['custo']) || !is_numeric($data['custo']) || $data['custo'] < 0) {
                throw new Exception('Custo deve ser um valor numérico válido');
            }
            
            // Verifica se o produto existe
            $this->db->query("SELECT id, tipo_condicao FROM products WHERE id = :product_id");
            $this->db->bind(':product_id', $data['product_id']);
            $product = $this->db->single();
            
            if (!$product) {
                throw new Exception('Produto não encontrado');
            }
            
            // Insere a movimentação de entrada
            $this->db->query("INSERT INTO inventory_moves (product_id, tipo, qtd, ref_origem, observacao) VALUES (:product_id, 'entrada', :qtd, :ref_origem, :observacao)");
            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':qtd', $data['qtd']);
            $this->db->bind(':ref_origem', 'Entrada Manual');
            $this->db->bind(':observacao', $data['observacao'] ?? '');
            $this->db->execute();
            $moveId = $this->db->lastInsertId();

            // Para produtos novos, assumimos que cada entrada gera um item de estoque.
            // A lógica pode ser mais complexa (ex: um único stock_item com quantidade),
            // mas para o controle unitário de seminovos, este modelo é mais flexível.
            if($product->tipo_condicao == 'novo'){
                for($i = 0; $i < $data['qtd']; $i++){
                    $this->db->query("INSERT INTO stock_items (product_id, condicao, aquisicao_tipo, aquisicao_custo, status) VALUES (:product_id, 'novo', 'compra', :custo, 'em_estoque')");
                    $this->db->bind(':product_id', $data['product_id']);
                    $this->db->bind(':custo', $data['custo']);
                    $this->db->execute();
                }
            }
            
            $this->db->commit();
            return true;

        } catch (Exception $e){
            $this->db->rollBack();
            error_log("Erro em addStockMovement: " . $e->getMessage());
            throw $e; // Re-throw para que o controlador possa tratar
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

    public function getStockBalances(){
        $this->db->query("
            SELECT 
                p.id as product_id,
                p.nome as product_nome,
                p.sku,
                b.nome as brand_nome,
                p.tipo_condicao,
                COALESCE(
                    (SELECT SUM(qtd) FROM inventory_moves WHERE product_id = p.id AND tipo IN ('entrada', 'emprestimo_retorno')), 0
                ) - COALESCE(
                    (SELECT SUM(qtd) FROM inventory_moves WHERE product_id = p.id AND tipo IN ('saida', 'emprestimo_saida', 'baixa_reserva')), 0
                ) as saldo_calculado,
                COUNT(si.id) as itens_fisicos,
                SUM(CASE WHEN si.status = 'em_estoque' THEN 1 ELSE 0 END) as itens_disponiveis,
                SUM(CASE WHEN si.status = 'reservado' THEN 1 ELSE 0 END) as itens_reservados,
                SUM(CASE WHEN si.status = 'emprestado' THEN 1 ELSE 0 END) as itens_emprestados,
                SUM(CASE WHEN si.status = 'vendido' THEN 1 ELSE 0 END) as itens_vendidos
            FROM products p
            JOIN brands b ON p.brand_id = b.id
            LEFT JOIN stock_items si ON p.id = si.product_id
            GROUP BY p.id, p.nome, p.sku, b.nome, p.tipo_condicao
            HAVING saldo_calculado > 0 OR itens_fisicos > 0
            ORDER BY p.nome ASC
        ");
        return $this->db->resultSet();
    }

    public function getProductStockBalance($product_id){
        $this->db->query("
            SELECT 
                COALESCE(
                    (SELECT SUM(qtd) FROM inventory_moves WHERE product_id = :product_id AND tipo IN ('entrada', 'emprestimo_retorno')), 0
                ) - COALESCE(
                    (SELECT SUM(qtd) FROM inventory_moves WHERE product_id = :product_id AND tipo IN ('saida', 'emprestimo_saida', 'baixa_reserva')), 0
                ) as saldo_calculado
        ");
        $this->db->bind(':product_id', $product_id);
        $result = $this->db->single();
        return $result ? $result->saldo_calculado : 0;
    }

    public function getAvailableStockItems($product_id){
        $this->db->query("
            SELECT * FROM stock_items 
            WHERE product_id = :product_id AND status = 'em_estoque'
            ORDER BY id ASC
        ");
        $this->db->bind(':product_id', $product_id);
        return $this->db->resultSet();
    }

    public function markStockItemAsSold($stock_item_id, $order_id = null){
        $this->db->beginTransaction();
        try {
            // Atualiza o status do item para vendido
            $this->db->query("UPDATE stock_items SET status = 'vendido' WHERE id = :stock_item_id");
            $this->db->bind(':stock_item_id', $stock_item_id);
            $this->db->execute();

            // Busca informações do item para criar movimentação
            $this->db->query("SELECT product_id FROM stock_items WHERE id = :stock_item_id");
            $this->db->bind(':stock_item_id', $stock_item_id);
            $stockItem = $this->db->single();

            if($stockItem){
                // Cria movimentação de saída
                $this->db->query("INSERT INTO inventory_moves (product_id, stock_item_id, tipo, qtd, ref_origem, id_origem, observacao) VALUES (:product_id, :stock_item_id, 'saida', 1, 'Venda', :order_id, 'Item vendido')");
                $this->db->bind(':product_id', $stockItem->product_id);
                $this->db->bind(':stock_item_id', $stock_item_id);
                $this->db->bind(':order_id', $order_id);
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
}
