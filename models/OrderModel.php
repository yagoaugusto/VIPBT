<?php

use core\Database;

class OrderModel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllOrders(){
        $this->db->query("
            SELECT 
                orders.*, 
                customers.nome as customer_nome, 
                u.nome as seller_nome
            FROM orders
            JOIN customers ON orders.customer_id = customers.id
            JOIN sellers ON orders.seller_id = sellers.id
            JOIN users u ON sellers.user_id = u.id
            ORDER BY orders.data DESC, orders.id DESC
        ");
        return $this->db->resultSet();
    }

    public function getOrderById($id){
        $this->db->query("
            SELECT 
                orders.*,
                c.nome as customer_nome,
                c.telefone as customer_telefone,
                u.nome as seller_nome,
                ch.nome as channel_nome
            FROM orders
            JOIN customers c ON orders.customer_id = c.id
            JOIN sellers s ON orders.seller_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN channels ch ON orders.channel_id = ch.id
            WHERE orders.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getOrderItems($order_id){
        $this->db->query("
            SELECT 
                oi.*,
                p.nome as product_nome,
                p.sku
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $this->db->bind(':order_id', $order_id);
        return $this->db->resultSet();
    }

    public function addOrder($data){
        $this->db->beginTransaction();

        try {
            $public_code = $this->generatePublicCode();

            $this->db->query("INSERT INTO orders (customer_id, seller_id, channel_id, data, public_code, observacao) VALUES (:customer_id, :seller_id, :channel_id, :data, :public_code, :observacao)");
            
            $this->db->bind(':customer_id', $data['customer_id']);
            $this->db->bind(':seller_id', $data['seller_id']);
            $this->db->bind(':channel_id', $data['channel_id']);
            $this->db->bind(':data', $data['data']);
            $this->db->bind(':public_code', $public_code);
            $this->db->bind(':observacao', $data['observacao']);
            
            $this->db->execute();
            $orderId = $this->db->lastInsertId();

            $orderTotal = 0;
            if(isset($data['items']) && is_array($data['items'])){
                // Carrega o modelo de estoque para reduzir o estoque
                require_once __DIR__ . '/StockModel.php';
                $stockModel = new StockModel();
                
                foreach($data['items'] as $item){
                    // Verifica disponibilidade antes de processar o item
                    $availableQtd = $stockModel->getProductStockBalance($item['id']);
                    
                    if($availableQtd < $item['qtd']){
                        // Se não há estoque suficiente, lança uma exceção
                        throw new Exception("Estoque insuficiente para o produto ID {$item['id']}. Disponível: {$availableQtd}, Solicitado: {$item['qtd']}");
                    }
                    
                    $this->db->query("INSERT INTO order_items (order_id, product_id, qtd, preco_unit, desconto) VALUES (:order_id, :product_id, :qtd, :preco_unit, :desconto)");
                    $this->db->bind(':order_id', $orderId);
                    $this->db->bind(':product_id', $item['id']);
                    $this->db->bind(':qtd', $item['qtd']);
                    $this->db->bind(':preco_unit', $item['preco']);
                    $this->db->bind(':desconto', $item['desconto']);
                    $this->db->execute();
                    $orderTotal += ($item['preco'] * $item['qtd']) - $item['desconto'];
                    
                    // Reduz o estoque: marca itens como vendidos
                    $availableItems = $stockModel->getAvailableStockItems($item['id']);
                    $qtdToSell = $item['qtd'];
                    
                    foreach($availableItems as $stockItem){
                        if($qtdToSell <= 0) break;
                        
                        $stockModel->markStockItemAsSold($stockItem->id, $orderId);
                        $qtdToSell--;
                    }
                    
                    // Se não há itens físicos suficientes, ainda registra a movimentação
                    if($qtdToSell > 0){
                        $this->db->query("INSERT INTO inventory_moves (product_id, tipo, qtd, ref_origem, id_origem, observacao) VALUES (:product_id, 'saida', :qtd, 'Venda', :order_id, 'Venda sem item físico específico')");
                        $this->db->bind(':product_id', $item['id']);
                        $this->db->bind(':qtd', $qtdToSell);
                        $this->db->bind(':order_id', $orderId);
                        $this->db->execute();
                    }
                }
            }

            // Processa créditos de trade-in
            if(!empty($data['tradeins'])){
                require_once __DIR__ . '/TradeInModel.php';
                $tradeInModel = new TradeInModel();
                
                foreach($data['tradeins'] as $tradein){
                    // Aplica o crédito de trade-in
                    $this->db->query("INSERT INTO order_credits (order_id, origem, descricao, valor, trade_in_id) VALUES (:order_id, 'trade_in', :descricao, :valor, :trade_in_id)");
                    $this->db->bind(':order_id', $orderId);
                    $this->db->bind(':descricao', 'Crédito de Trade-in #' . $tradein['id']);
                    $this->db->bind(':valor', $tradein['credit']);
                    $this->db->bind(':trade_in_id', $tradein['id']);
                    $this->db->execute();
                }
            }

            // Calcula total final com créditos
            $totalCredits = $data['total_credits'] ?? 0;
            $finalTotal = max(0, $orderTotal - $totalCredits);

            // Cria o registro de contas a receber
            $this->db->query("INSERT INTO receivables (order_id, valor_total, valor_a_receber) VALUES (:order_id, :valor_total, :valor_a_receber)");
            $this->db->bind(':order_id', $orderId);
            $this->db->bind(':valor_total', $orderTotal);
            $this->db->bind(':valor_a_receber', $finalTotal);
            $this->db->execute();

            // Atualiza o total do pedido
            $this->db->query("UPDATE orders SET total = :total WHERE id = :id");
            $this->db->bind(':total', $orderTotal);
            $this->db->bind(':id', $orderId);
            $this->db->execute();

            $this->db->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    private function generatePublicCode($length = 8) {
        do {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            $code = 'BT-' . $randomString;
            
            $this->db->query("SELECT id FROM orders WHERE public_code = :public_code");
            $this->db->bind(':public_code', $code);
            $this->db->execute();
        } while ($this->db->rowCount() > 0);
        
        return $code;
    }

    public function applyCreditToOrder($order_id, $credit_value, $trade_in_id = null){
        $this->db->beginTransaction();
        try {
            // Adiciona o crédito na tabela order_credits
            $description = 'Crédito de Trade-in';
            if($trade_in_id){
                $description .= ' #' . $trade_in_id;
            }
            $this->db->query("INSERT INTO order_credits (order_id, origem, descricao, valor) VALUES (:order_id, 'trade_in', :descricao, :valor)");
            $this->db->bind(':order_id', $order_id);
            $this->db->bind(':descricao', $description);
            $this->db->bind(':valor', $credit_value);
            $this->db->execute();

            // Atualiza o valor a receber do pedido
            $this->db->query("UPDATE receivables SET valor_a_receber = valor_a_receber - :credit_value WHERE order_id = :order_id");
            $this->db->bind(':credit_value', $credit_value);
            $this->db->bind(':order_id', $order_id);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e){
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function getOrderFulfillments($order_id){
        $this->db->query("SELECT * FROM fulfillments WHERE order_id = :order_id ORDER BY created_at ASC");
        $this->db->bind(':order_id', $order_id);
        return $this->db->resultSet();
    }

    public function getOrderByPublicCode($public_code){
        $this->db->query("
            SELECT 
                orders.*,
                c.nome as customer_nome,
                c.telefone as customer_telefone,
                c.cpf as customer_cpf,
                u.nome as seller_nome,
                ch.nome as channel_nome
            FROM orders
            JOIN customers c ON orders.customer_id = c.id
            JOIN sellers s ON orders.seller_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN channels ch ON orders.channel_id = ch.id
            WHERE orders.public_code = :public_code
        ");
        $this->db->bind(':public_code', $public_code);
        return $this->db->single();
    }
}
