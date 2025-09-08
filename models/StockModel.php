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
                ORDER BY 
                    CASE si.status 
                        WHEN 'emprestado' THEN 1
                        WHEN 'reservado' THEN 2
                        WHEN 'em_estoque' THEN 3
                        WHEN 'vendido' THEN 4
                        ELSE 5
                    END,
                    p.nome ASC,
                    si.id ASC
        ");
        return $this->db->resultSet();
    }

    public function addStockMovement($data){
        $startedTransaction = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }
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
            
            // Normaliza valores
            $qtd = (int)$data['qtd'];
            $custo = (float)$data['custo'];
            $precoVenda = (isset($data['preco_venda']) && $data['preco_venda'] !== '' && is_numeric($data['preco_venda'])) ? (float)$data['preco_venda'] : null;

            // Insere a movimentação de entrada
            $this->db->query("INSERT INTO inventory_moves (product_id, tipo, qtd, ref_origem, observacao) VALUES (:product_id, 'entrada', :qtd, :ref_origem, :observacao)");
            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':qtd', $qtd);
            $this->db->bind(':ref_origem', 'Entrada Manual');
            $this->db->bind(':observacao', $data['observacao'] ?? '');
            $this->db->execute();
            $moveId = $this->db->lastInsertId();

            // Para qualquer tipo de produto, cada unidade gera um item físico em estoque
            $condicao = $product->tipo_condicao; // 'novo' ou 'seminovo'
            for($i = 0; $i < $qtd; $i++){
                $this->db->query("INSERT INTO stock_items (product_id, condicao, aquisicao_tipo, aquisicao_custo, status, preco_venda) VALUES (:product_id, :condicao, 'compra', :custo, 'em_estoque', :preco_venda)");
                $this->db->bind(':product_id', $data['product_id']);
                $this->db->bind(':condicao', $condicao);
                $this->db->bind(':custo', $custo);
                $this->db->bind(':preco_venda', $precoVenda);
                $this->db->execute();
            }
            
            if ($startedTransaction) {
                $this->db->commit();
            }
            return true;

        } catch (Exception $e){
            if ($startedTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
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

    // Itens físicos disponíveis para venda (qualquer condição), com preço por item
    public function getAllAvailableStockItemsForSale(){
        $this->db->query(" 
            SELECT 
                si.id as stock_item_id,
                si.product_id,
                si.preco_venda,
                si.condicao,
                si.serie,
                si.grade,
                p.nome as product_nome,
                p.sku,
                b.nome as brand_nome
            FROM stock_items si
            JOIN products p ON si.product_id = p.id
            JOIN brands b ON p.brand_id = b.id
            WHERE si.status = 'em_estoque'
            ORDER BY p.nome ASC, si.id ASC
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
                        (SELECT SUM(qtd) FROM inventory_moves WHERE product_id = :product_id_in AND tipo IN ('entrada', 'emprestimo_retorno')), 0
                    ) - COALESCE(
                        (SELECT SUM(qtd) FROM inventory_moves WHERE product_id = :product_id_out AND tipo IN ('saida', 'emprestimo_saida', 'baixa_reserva')), 0
                    ) as saldo_calculado
        ");
            $this->db->bind(':product_id_in', $product_id);
            $this->db->bind(':product_id_out', $product_id);
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
        $startedTransaction = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }
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

            if ($startedTransaction) {
                $this->db->commit();
            }
            return true;
        } catch (Exception $e){
            if ($startedTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Erro em markStockItemAsSold: " . $e->getMessage());
            return false;
        }
    }

    // Marca item como reservado e cria movimentação de baixa de reserva
    public function markStockItemAsReserved($stock_item_id, $order_id){
        $startedTransaction = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }
        try {
            // Atualiza o status do item para reservado
            $this->db->query("UPDATE stock_items SET status = 'reservado' WHERE id = :stock_item_id AND status = 'em_estoque'");
            $this->db->bind(':stock_item_id', $stock_item_id);
            $this->db->execute();

            if ($this->db->rowCount() === 0) {
                throw new Exception('Item de estoque não disponível para reserva.');
            }

            // Busca informações do item para criar movimentação
            $this->db->query("SELECT product_id FROM stock_items WHERE id = :stock_item_id");
            $this->db->bind(':stock_item_id', $stock_item_id);
            $stockItem = $this->db->single();

            if($stockItem){
                // Cria movimentação de baixa de reserva (contabiliza como saída)
                $this->db->query("INSERT INTO inventory_moves (product_id, stock_item_id, tipo, qtd, ref_origem, id_origem, observacao) VALUES (:product_id, :stock_item_id, 'baixa_reserva', 1, 'Reserva Pedido', :order_id, 'Item reservado para pedido')");
                $this->db->bind(':product_id', $stockItem->product_id);
                $this->db->bind(':stock_item_id', $stock_item_id);
                $this->db->bind(':order_id', $order_id);
                $this->db->execute();
            }

            if ($startedTransaction) {
                $this->db->commit();
            }
            return true;
        } catch (Exception $e){
            if ($startedTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Erro em markStockItemAsReserved: " . $e->getMessage());
            throw $e;
        }
    }

    // Reserva até qtd itens disponíveis de um produto; retorna quantidade reservada
    public function reserveAvailableItems($product_id, $qtd, $order_id){
        $this->db->query("SELECT id FROM stock_items WHERE product_id = :pid AND status = 'em_estoque' ORDER BY id ASC LIMIT :limit");
        $this->db->bind(':pid', $product_id);
        // bind limit como int
        $limit = (int)$qtd;
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);
        $items = $this->db->resultSet();
        $count = 0;
        foreach($items as $it){
            $this->markStockItemAsReserved($it->id, $order_id);
            $count++;
        }
        return $count;
    }

    // Converte reservas de um pedido em vendas efetivas
    public function markReservedItemsAsSoldForOrder($order_id){
        $startedTransaction = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }
        try {
            // Seleciona as reservas deste pedido
            $this->db->query("SELECT stock_item_id, product_id FROM inventory_moves WHERE tipo = 'baixa_reserva' AND id_origem = :order_id");
            $this->db->bind(':order_id', $order_id);
            $reservas = $this->db->resultSet();

            foreach($reservas as $res){
                if (!is_null($res->stock_item_id)) {
                    // Atualiza o status do item para vendido
                    $this->db->query("UPDATE stock_items SET status = 'vendido' WHERE id = :sid");
                    $this->db->bind(':sid', $res->stock_item_id);
                    $this->db->execute();

                    // Converte a movimentação de baixa de reserva em saída definitiva
                    $this->db->query("UPDATE inventory_moves SET tipo = 'saida', ref_origem = 'Venda', observacao = 'Reserva convertida em venda' WHERE tipo = 'baixa_reserva' AND id_origem = :order_id AND stock_item_id = :sid");
                    $this->db->bind(':order_id', $order_id);
                    $this->db->bind(':sid', $res->stock_item_id);
                    $this->db->execute();
                }
            }

            // Converte reservas genéricas (sem stock_item_id) em saídas definitivas
            $this->db->query("UPDATE inventory_moves SET tipo = 'saida', ref_origem = 'Venda', observacao = 'Reserva (genérica) convertida em venda' WHERE tipo = 'baixa_reserva' AND id_origem = :order_id AND stock_item_id IS NULL");
            $this->db->bind(':order_id', $order_id);
            $this->db->execute();

            if ($startedTransaction) {
                $this->db->commit();
            }
            return true;
        } catch (Exception $e){
            if ($startedTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Erro em markReservedItemsAsSoldForOrder: " . $e->getMessage());
            throw $e;
        }
    }

    // Libera reservas (volta itens para em_estoque) e marca movimentos como reserva_cancelada
    public function releaseReservedItemsForOrder($order_id){
        $startedTransaction = false;
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
            $startedTransaction = true;
        }
        try {
            $this->db->query("SELECT stock_item_id FROM inventory_moves WHERE tipo = 'baixa_reserva' AND id_origem = :order_id");
            $this->db->bind(':order_id', $order_id);
            $reservas = $this->db->resultSet();

            foreach($reservas as $res){
                $this->db->query("UPDATE stock_items SET status = 'em_estoque' WHERE id = :sid AND status = 'reservado'");
                $this->db->bind(':sid', $res->stock_item_id);
                $this->db->execute();
            }

            $this->db->query("UPDATE inventory_moves SET tipo = 'reserva_cancelada', observacao = 'Reserva cancelada' WHERE tipo = 'baixa_reserva' AND id_origem = :order_id");
            $this->db->bind(':order_id', $order_id);
            $this->db->execute();

            if ($startedTransaction) {
                $this->db->commit();
            }
            return true;
        } catch (Exception $e){
            if ($startedTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Erro em releaseReservedItemsForOrder: " . $e->getMessage());
            throw $e;
        }
    }

    // Método auxiliar para verificar disponibilidade de estoque de múltiplos produtos
    public function checkMultipleProductsAvailability($items){
        $availability = [];
        foreach($items as $item){
            $productId = $item['id'] ?? $item['product_id'];
            $requestedQtd = $item['qtd'] ?? $item['quantidade'];
            
            $availableQtd = $this->getProductStockBalance($productId);
            $availability[] = [
                'product_id' => $productId,
                'requested' => $requestedQtd,
                'available' => $availableQtd,
                'sufficient' => $availableQtd >= $requestedQtd
            ];
        }
        return $availability;
    }
}
