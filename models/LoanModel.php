<?php

use core\Database;

class LoanModel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllLoans(){
        $this->db->query("
            SELECT 
                l.*,
                c.nome as customer_nome,
                u.nome as vendedor_nome
            FROM loans l
            JOIN customers c ON l.customer_id = c.id
            JOIN users u ON l.vendedor_user_id = u.id
            ORDER BY l.data_saida DESC
        ");
        return $this->db->resultSet();
    }

    public function getLoanById($id){
        $this->db->query("
            SELECT 
                l.*,
                c.nome as customer_nome,
                u.nome as vendedor_nome
            FROM loans l
            JOIN customers c ON l.customer_id = c.id
            JOIN users u ON l.vendedor_user_id = u.id
            WHERE l.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getLoanItems($loan_id){
        $this->db->query("
            SELECT 
                li.*,
                si.serie,
                p.nome as product_nome,
                p.sku
            FROM loan_items li
            JOIN stock_items si ON li.stock_item_id = si.id
            JOIN products p ON si.product_id = p.id
            WHERE li.loan_id = :loan_id
        ");
        $this->db->bind(':loan_id', $loan_id);
        return $this->db->resultSet();
    }

    public function addLoan($data){
        file_put_contents('/tmp/loan_debug.log', "--- addLoan called ---\n", FILE_APPEND);
        file_put_contents('/tmp/loan_debug.log', "Data received: " . print_r($data, true) . "\n", FILE_APPEND);

        $this->db->beginTransaction();
        try {
            // Insere o empréstimo
            $sql = "INSERT INTO loans (customer_id, vendedor_user_id, data_saida, data_prevista_retorno, observacoes) VALUES (:customer_id, :vendedor_user_id, :data_saida, :data_prevista_retorno, :observacoes)";
            file_put_contents('/tmp/loan_debug.log', "SQL (loans): " . $sql . "\n", FILE_APPEND);
            $this->db->query($sql);
            $this->db->bind(':customer_id', $data['customer_id']);
            $this->db->bind(':vendedor_user_id', $data['vendedor_user_id']);
            $this->db->bind(':data_saida', $data['data_saida']);
            $this->db->bind(':data_prevista_retorno', $data['data_prevista_retorno']);
            $this->db->bind(':observacoes', $data['observacoes']);
            file_put_contents('/tmp/loan_debug.log', "Bindings (loans): " . json_encode([
                ':customer_id' => $data['customer_id'],
                ':vendedor_user_id' => $data['vendedor_user_id'],
                ':data_saida' => $data['data_saida'],
                ':data_prevista_retorno' => $data['data_prevista_retorno'],
                ':observacoes' => $data['observacoes']
            ]) . "\n", FILE_APPEND);
            $this->db->execute();
            $loanId = $this->db->lastInsertId();
            file_put_contents('/tmp/loan_debug.log', "Loan ID inserted: " . $loanId . "\n", FILE_APPEND);

            // Insere os itens do empréstimo e atualiza o status do stock_item
            foreach($data['items'] as $index => $item){
                file_put_contents('/tmp/loan_debug.log', "--- Processing item " . $index . " ---\n", FILE_APPEND);
                file_put_contents('/tmp/loan_debug.log', "Item data: " . print_r($item, true) . "\n", FILE_APPEND);

                $sql = "INSERT INTO loan_items (loan_id, stock_item_id, estado_saida) VALUES (:loan_id, :stock_item_id, :estado_saida)";
                file_put_contents('/tmp/loan_debug.log', "SQL (loan_items): " . $sql . "\n", FILE_APPEND);
                $this->db->query($sql);
                $this->db->bind(':loan_id', $loanId);
                $this->db->bind(':stock_item_id', $item['stock_item_id']);
                $this->db->bind(':estado_saida', $item['estado_saida']);
                file_put_contents('/tmp/loan_debug.log', "Bindings (loan_items): " . json_encode([
                    ':loan_id' => $loanId,
                    ':stock_item_id' => $item['stock_item_id'],
                    ':estado_saida' => $item['estado_saida']
                ]) . "\n", FILE_APPEND);
                $this->db->execute();

                $sql = "UPDATE stock_items SET status = 'emprestado' WHERE id = :stock_item_id";
                file_put_contents('/tmp/loan_debug.log', "SQL (update stock_items): " . $sql . "\n", FILE_APPEND);
                $this->db->query($sql);
                $this->db->bind(':stock_item_id', $item['stock_item_id']);
                file_put_contents('/tmp/loan_debug.log', "Bindings (update stock_items): " . json_encode([
                    ':stock_item_id' => $item['stock_item_id']
                ]) . "\n", FILE_APPEND);
                $this->db->execute();

                $sql = "INSERT INTO inventory_moves (product_id, stock_item_id, tipo, qtd, ref_origem, id_origem, observacao) VALUES (:product_id, :stock_item_id, 'emprestimo_saida', 1, 'Empréstimo', :loan_id, :observacao)";
                file_put_contents('/tmp/loan_debug.log', "SQL (inventory_moves): " . $sql . "\n", FILE_APPEND);
                $this->db->query($sql);
                $this->db->bind(':product_id', $item['product_id']); // Precisa do product_id do stock_item
                $this->db->bind(':stock_item_id', $item['stock_item_id']);
                $this->db->bind(':loan_id', $loanId);
                $this->db->bind(':observacao', 'Saída para empréstimo de teste');
                file_put_contents('/tmp/loan_debug.log', "Bindings (inventory_moves): " . json_encode([
                    ':product_id' => $item['product_id'],
                    ':stock_item_id' => $item['stock_item_id'],
                    ':loan_id' => $loanId,
                    ':observacao' => 'Saída para empréstimo de teste'
                ]) . "\n", FILE_APPEND);
                $this->db->execute();
            }

            $this->db->commit();
            file_put_contents('/tmp/loan_debug.log', "Transaction committed successfully.\n", FILE_APPEND);
            return $loanId;
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e->getMessage());
            file_put_contents('/tmp/loan_error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
            file_put_contents('/tmp/loan_debug.log', "Transaction rolled back. Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }

    public function returnLoanItem($loan_id, $stock_item_id, $estado_retorno){
        $this->db->beginTransaction();
        try {
            // Atualiza o estado de retorno do item de empréstimo
            $this->db->query("UPDATE loan_items SET estado_retorno = :estado_retorno WHERE loan_id = :loan_id AND stock_item_id = :stock_item_id");
            $this->db->bind(':estado_retorno', $estado_retorno);
            $this->db->bind(':loan_id', $loan_id);
            $this->db->bind(':stock_item_id', $stock_item_id);
            $this->db->execute();

            // Atualiza o status do stock_item para 'em_estoque'
            $this->db->query("UPDATE stock_items SET status = 'em_estoque' WHERE id = :stock_item_id");
            $this->db->bind(':stock_item_id', $stock_item_id);
            $this->db->execute();

            // Registra a movimentação de retorno no inventário
            $this->db->query("INSERT INTO inventory_moves (product_id, stock_item_id, tipo, qtd, ref_origem, id_origem, observacao) VALUES (:product_id, :stock_item_id, 'emprestimo_retorno', 1, 'Devolução de Empréstimo', :loan_id, :observacao)");
            $this->db->bind(':product_id', $this->getProductIdFromStockItem($stock_item_id)); // Busca o product_id
            $this->db->bind(':stock_item_id', $stock_item_id);
            $this->db->bind(':loan_id', $loan_id);
            $this->db->bind(':observacao', 'Devolução de empréstimo de teste');
            $this->db->execute();

            // Verifica se todos os itens do empréstimo foram devolvidos
            $this->db->query("SELECT COUNT(*) as total_items FROM loan_items WHERE loan_id = :loan_id");
            $this->db->bind(':loan_id', $loan_id);
            $totalItems = $this->db->single()->total_items;

            $this->db->query("SELECT COUNT(*) as returned_items FROM loan_items WHERE loan_id = :loan_id AND estado_retorno IS NOT NULL");
            $this->db->bind(':loan_id', $loan_id);
            $returnedItems = $this->db->single()->returned_items;

            if($totalItems == $returnedItems){
                // Se todos os itens foram devolvidos, atualiza o status do empréstimo para 'devolvido'
                $this->db->query("UPDATE loans SET status = 'devolvido', data_retorno = CURDATE() WHERE id = :loan_id");
                $this->db->bind(':loan_id', $loan_id);
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

    // Converte empréstimo em venda - Regra de Negócio 4.3
    public function convertLoanToSale($loan_id, $customer_id, $seller_id, $channel_id){
        $this->db->beginTransaction();
        try {
            // Busca detalhes do empréstimo
            $loan = $this->getLoanById($loan_id);
            if(!$loan || $loan->status != 'ativo'){
                throw new Exception('Empréstimo não encontrado ou não está ativo');
            }

            // Busca itens do empréstimo
            $loanItems = $this->getLoanItems($loan_id);
            if(empty($loanItems)){
                throw new Exception('Empréstimo não possui itens');
            }

            // Cria um pedido automaticamente
            require_once __DIR__ . '/OrderModel.php';
            $orderModel = new OrderModel();

            // Prepara dados do pedido
            $orderData = [
                'customer_id' => $customer_id,
                'seller_id' => $seller_id,
                'channel_id' => $channel_id,
                'data' => date('Y-m-d'),
                'observacao' => 'Conversão de empréstimo #' . $loan_id,
                'items' => []
            ];

            // Adiciona itens do empréstimo ao pedido
            foreach($loanItems as $item){
                // Busca informações do produto
                $this->db->query("
                    SELECT p.id, p.preco 
                    FROM products p 
                    JOIN stock_items si ON p.id = si.product_id 
                    WHERE si.id = :stock_item_id
                ");
                $this->db->bind(':stock_item_id', $item->stock_item_id);
                $product = $this->db->single();

                if($product){
                    $orderData['items'][] = [
                        'id' => $product->id,
                        'qtd' => 1,
                        'preco' => $product->preco,
                        'desconto' => 0
                    ];
                }
            }

            // Cria o pedido
            $orderId = $orderModel->addOrder($orderData);
            if(!$orderId){
                throw new Exception('Erro ao criar pedido da conversão');
            }

            // Atualiza status do empréstimo
            $this->db->query("UPDATE loans SET status = 'convertido_em_venda', order_id = :order_id WHERE id = :loan_id");
            $this->db->bind(':order_id', $orderId);
            $this->db->bind(':loan_id', $loan_id);
            $this->db->execute();

            // Marca itens como vendidos (já foi feito no OrderModel)
            // Não precisa fazer nada extra com os stock_items pois o addOrder já cuida disso

            $this->db->commit();
            return $orderId;
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e->getMessage());
            throw $e; // Re-throw para o controlador tratar
        }
    }

    public function getLoanById($loan_id){
        $this->db->query("
            SELECT l.*, c.nome as customer_nome, u.nome as user_nome
            FROM loans l
            JOIN customers c ON l.customer_id = c.id
            JOIN users u ON l.user_id = u.id
            WHERE l.id = :loan_id
        ");
        $this->db->bind(':loan_id', $loan_id);
        return $this->db->single();
    }

    public function getLoanItems($loan_id){
        $this->db->query("
            SELECT li.*, si.serie, p.nome as product_nome, p.sku
            FROM loan_items li
            JOIN stock_items si ON li.stock_item_id = si.id
            JOIN products p ON si.product_id = p.id
            WHERE li.loan_id = :loan_id
        ");
        $this->db->bind(':loan_id', $loan_id);
        return $this->db->resultSet();
    }

    // Helper para buscar product_id de um stock_item
    private function getProductIdFromStockItem($stock_item_id){
        $this->db->query("SELECT product_id FROM stock_items WHERE id = :stock_item_id");
        $this->db->bind(':stock_item_id', $stock_item_id);
        return $this->db->single()->product_id;
    }
}
