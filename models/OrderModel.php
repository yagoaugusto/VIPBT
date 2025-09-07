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
                c.nome as customer_nome, 
                u.nome as seller_nome,
                ch.nome as channel_nome
            FROM orders
            LEFT JOIN customers c ON orders.customer_id = c.id
            LEFT JOIN sellers s ON orders.seller_id = s.id
            LEFT JOIN users u ON s.user_id = u.id
            LEFT JOIN channels ch ON orders.channel_id = ch.id
            ORDER BY orders.data DESC, orders.id DESC
        ");
        $orders = $this->db->resultSet();
        
        // Adiciona valores padrão para dados faltantes
        foreach ($orders as $order) {
            if (!$order->customer_nome) {
                $order->customer_nome = 'Cliente não encontrado';
            }
            if (!$order->seller_nome) {
                $order->seller_nome = 'Vendedor não encontrado';
            }
            if (!$order->channel_nome) {
                $order->channel_nome = 'Canal não encontrado';
            }
        }
        
        return $orders;
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
            LEFT JOIN customers c ON orders.customer_id = c.id
            LEFT JOIN sellers s ON orders.seller_id = s.id
            LEFT JOIN users u ON s.user_id = u.id
            LEFT JOIN channels ch ON orders.channel_id = ch.id
            WHERE orders.id = :id
        ");
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        
        // Se não encontrou o pedido, retorna null
        if (!$result) {
            return null;
        }
        
        // Verifica se há dados relacionados faltando e define valores padrão
        if (!$result->customer_nome) {
            $result->customer_nome = 'Cliente não encontrado';
        }
        if (!$result->seller_nome) {
            $result->seller_nome = 'Vendedor não encontrado';
        }
        if (!$result->channel_nome) {
            $result->channel_nome = 'Canal não encontrado';
        }
        
        return $result;
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
            // Check if required table columns exist
            $this->checkRequiredTableColumns();
            
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

            // Validate that the order was created successfully
            if (!$orderId) {
                throw new Exception("Falha ao criar o pedido na base de dados.");
            }

            $orderTotal = 0;
            if(isset($data['items']) && is_array($data['items'])){
                if (count($data['items']) === 0) {
                    throw new Exception("O pedido deve conter pelo menos um item.");
                }
                // Carrega o modelo de estoque para reduzir o estoque
                require_once __DIR__ . '/StockModel.php';
                $stockModel = new StockModel();
                
                foreach($data['items'] as $index => $item){
                    // Validate item data
                    if (!isset($item['id']) || !is_numeric($item['id']) || $item['id'] <= 0) {
                        throw new Exception("Item #" . ($index + 1) . ": ID do produto é obrigatório e deve ser um número válido.");
                    }
                    
                    if (!isset($item['qtd']) || !is_numeric($item['qtd']) || $item['qtd'] <= 0) {
                        throw new Exception("Item #" . ($index + 1) . ": Quantidade deve ser um número maior que zero.");
                    }
                    
                    if (!isset($item['preco']) || !is_numeric($item['preco']) || $item['preco'] < 0) {
                        throw new Exception("Item #" . ($index + 1) . ": Preço deve ser um valor numérico válido.");
                    }
                    
                    if (!isset($item['desconto']) || !is_numeric($item['desconto']) || $item['desconto'] < 0) {
                        throw new Exception("Item #" . ($index + 1) . ": Desconto deve ser um valor numérico válido.");
                    }
                    
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
            } else {
                throw new Exception("O pedido deve conter pelo menos um item.");
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
                    
                    // Marca o trade-in como creditado/usado
                    $tradeInModel->markTradeInAsUsed($tradein['id']);
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
            error_log("OrderModel::addOrder error: " . $e->getMessage());
            throw $e; // Re-throw the exception so the controller can handle it properly
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
            $this->db->query("INSERT INTO order_credits (order_id, origem, descricao, valor, trade_in_id) VALUES (:order_id, 'trade_in', :descricao, :valor, :trade_in_id)");
            $this->db->bind(':order_id', $order_id);
            $this->db->bind(':descricao', $description);
            $this->db->bind(':valor', $credit_value);
            $this->db->bind(':trade_in_id', $trade_in_id);
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
            LEFT JOIN customers c ON orders.customer_id = c.id
            LEFT JOIN sellers s ON orders.seller_id = s.id
            LEFT JOIN users u ON s.user_id = u.id
            LEFT JOIN channels ch ON orders.channel_id = ch.id
            WHERE orders.public_code = :public_code
        ");
        $this->db->bind(':public_code', $public_code);
        $result = $this->db->single();
        
        // Se não encontrou o pedido, retorna null
        if (!$result) {
            return null;
        }
        
        // Verifica se há dados relacionados faltando e define valores padrão
        if (!$result->customer_nome) {
            $result->customer_nome = 'Cliente não encontrado';
        }
        if (!$result->seller_nome) {
            $result->seller_nome = 'Vendedor não encontrado';
        }
        if (!$result->channel_nome) {
            $result->channel_nome = 'Canal não encontrado';
        }
        
        return $result;
    }

    public function getOrderCredits($order_id){
        $this->db->query("SELECT * FROM order_credits WHERE order_id = :order_id ORDER BY created_at ASC");
        $this->db->bind(':order_id', $order_id);
        return $this->db->resultSet();
    }

    public function updateOrderFiscalStatus($order_id, $status){
        $this->db->query("UPDATE orders SET status_fiscal = :status WHERE id = :order_id");
        $this->db->bind(':status', $status);
        $this->db->bind(':order_id', $order_id);
        return $this->db->execute();
    }

    public function updateOrderDeliveryStatus($order_id, $status){
        $this->db->query("UPDATE orders SET status_entrega = :status WHERE id = :order_id");
        $this->db->bind(':status', $status);
        $this->db->bind(':order_id', $order_id);
        return $this->db->execute();
    }

    // Confirma um pedido como venda realizada
    public function confirmOrderAsSale($order_id, $confirmed_by_user_id = null){
        $this->db->beginTransaction();
        try {
            // Atualiza o status do pedido para 'vendido'
            $this->db->query("
                UPDATE orders 
                SET status_pedido = 'vendido', 
                    data_confirmacao_venda = NOW(), 
                    confirmado_por = :confirmed_by
                WHERE id = :order_id AND status_pedido IN ('novo', 'confirmado')
            ");
            $this->db->bind(':order_id', $order_id);
            $this->db->bind(':confirmed_by', $confirmed_by_user_id);
            $this->db->execute();

            if ($this->db->rowCount() === 0) {
                throw new Exception("Pedido não encontrado ou já possui status que não permite confirmação como venda.");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("OrderModel::confirmOrderAsSale error: " . $e->getMessage());
            throw $e;
        }
    }

    // Atualiza status do pedido - mantém método genérico para flexibilidade
    public function updateOrderStatus($order_id, $status, $confirmed_by_user_id = null){
        $allowed_statuses = ['novo', 'confirmado', 'vendido', 'cancelado'];
        
        if (!in_array($status, $allowed_statuses)) {
            throw new Exception("Status inválido: $status");
        }

        $this->db->beginTransaction();
        try {
            $sql = "UPDATE orders SET status_pedido = :status";
            
            // Se está confirmando como venda, registra data e usuário
            if ($status === 'vendido') {
                $sql .= ", data_confirmacao_venda = NOW(), confirmado_por = :confirmed_by";
            }
            
            $sql .= " WHERE id = :order_id";
            
            $this->db->query($sql);
            $this->db->bind(':status', $status);
            $this->db->bind(':order_id', $order_id);
            
            if ($status === 'vendido') {
                $this->db->bind(':confirmed_by', $confirmed_by_user_id);
            }
            
            $this->db->execute();

            if ($this->db->rowCount() === 0) {
                throw new Exception("Pedido não encontrado.");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("OrderModel::updateOrderStatus error: " . $e->getMessage());
            throw $e;
        }
    }

    // Obtém estatísticas de conversão de pedidos para vendas
    public function getOrderConversionStats($start_date = null, $end_date = null, $channel_id = null, $seller_id = null){
        $where_conditions = [];
        $params = [];

        if ($start_date) {
            $where_conditions[] = "o.data >= :start_date";
            $params[':start_date'] = $start_date;
        }

        if ($end_date) {
            $where_conditions[] = "o.data <= :end_date";
            $params[':end_date'] = $end_date;
        }

        if ($channel_id) {
            $where_conditions[] = "o.channel_id = :channel_id";
            $params[':channel_id'] = $channel_id;
        }

        if ($seller_id) {
            $where_conditions[] = "o.seller_id = :seller_id";
            $params[':seller_id'] = $seller_id;
        }

        $where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" AND ", $where_conditions);

        $this->db->query("
            SELECT 
                COUNT(*) as total_pedidos,
                SUM(CASE WHEN o.status_pedido = 'vendido' THEN 1 ELSE 0 END) as vendas_confirmadas,
                SUM(CASE WHEN o.status_pedido = 'cancelado' THEN 1 ELSE 0 END) as pedidos_cancelados,
                SUM(CASE WHEN o.status_pedido IN ('novo', 'confirmado') THEN 1 ELSE 0 END) as pedidos_pendentes,
                ROUND(
                    (SUM(CASE WHEN o.status_pedido = 'vendido' THEN 1 ELSE 0 END) * 100.0) / 
                    NULLIF(COUNT(*), 0), 2
                ) as taxa_conversao_percent
            FROM orders o
            $where_clause
        ");

        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        return $this->db->single();
    }

    // Obtém estatísticas detalhadas por canal de venda
    public function getConversionStatsByChannel($start_date = null, $end_date = null){
        $where_conditions = [];
        $params = [];

        if ($start_date) {
            $where_conditions[] = "o.data >= :start_date";
            $params[':start_date'] = $start_date;
        }

        if ($end_date) {
            $where_conditions[] = "o.data <= :end_date";
            $params[':end_date'] = $end_date;
        }

        $where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" AND ", $where_conditions);

        $this->db->query("
            SELECT 
                c.nome as canal_nome,
                c.id as canal_id,
                COUNT(*) as total_pedidos,
                SUM(CASE WHEN o.status_pedido = 'vendido' THEN 1 ELSE 0 END) as vendas_confirmadas,
                SUM(CASE WHEN o.status_pedido = 'cancelado' THEN 1 ELSE 0 END) as pedidos_cancelados,
                SUM(CASE WHEN o.status_pedido IN ('novo', 'confirmado') THEN 1 ELSE 0 END) as pedidos_pendentes,
                ROUND(
                    (SUM(CASE WHEN o.status_pedido = 'vendido' THEN 1 ELSE 0 END) * 100.0) / 
                    NULLIF(COUNT(*), 0), 2
                ) as taxa_conversao_percent,
                SUM(CASE WHEN o.status_pedido = 'vendido' THEN IFNULL(o.total, 0) ELSE 0 END) as valor_total_vendas
            FROM orders o
            LEFT JOIN channels c ON o.channel_id = c.id
            $where_clause
            GROUP BY c.id, c.nome
            ORDER BY vendas_confirmadas DESC
        ");

        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        return $this->db->resultSet();
    }

    // Obtém estatísticas detalhadas por vendedor
    public function getConversionStatsBySeller($start_date = null, $end_date = null){
        $where_conditions = [];
        $params = [];

        if ($start_date) {
            $where_conditions[] = "o.data >= :start_date";
            $params[':start_date'] = $start_date;
        }

        if ($end_date) {
            $where_conditions[] = "o.data <= :end_date";
            $params[':end_date'] = $end_date;
        }

        $where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" AND ", $where_conditions);

        $this->db->query("
            SELECT 
                u.nome as vendedor_nome,
                s.id as vendedor_id,
                COUNT(*) as total_pedidos,
                SUM(CASE WHEN o.status_pedido = 'vendido' THEN 1 ELSE 0 END) as vendas_confirmadas,
                SUM(CASE WHEN o.status_pedido = 'cancelado' THEN 1 ELSE 0 END) as pedidos_cancelados,
                SUM(CASE WHEN o.status_pedido IN ('novo', 'confirmado') THEN 1 ELSE 0 END) as pedidos_pendentes,
                ROUND(
                    (SUM(CASE WHEN o.status_pedido = 'vendido' THEN 1 ELSE 0 END) * 100.0) / 
                    NULLIF(COUNT(*), 0), 2
                ) as taxa_conversao_percent,
                SUM(CASE WHEN o.status_pedido = 'vendido' THEN IFNULL(o.total, 0) ELSE 0 END) as valor_total_vendas
            FROM orders o
            LEFT JOIN sellers s ON o.seller_id = s.id
            LEFT JOIN users u ON s.user_id = u.id
            $where_clause
            GROUP BY s.id, u.nome
            ORDER BY vendas_confirmadas DESC
        ");

        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        return $this->db->resultSet();
    }

    // Check if required database columns exist
    private function checkRequiredTableColumns(){
        // Check if order_credits table has trade_in_id column
        $this->db->query("
            SELECT COUNT(*) as column_exists 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'order_credits' 
            AND COLUMN_NAME = 'trade_in_id'
        ");
        $result = $this->db->single();
        
        if (!$result || $result->column_exists == 0) {
            throw new Exception("Database schema incomplete: order_credits table missing trade_in_id column. Please run database updates.");
        }

        // Check if orders table has total column
        $this->db->query("
            SELECT COUNT(*) as column_exists 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'orders' 
            AND COLUMN_NAME = 'total'
        ");
        $result = $this->db->single();
        
        if (!$result || $result->column_exists == 0) {
            throw new Exception("Database schema incomplete: orders table missing total column. Please run database updates.");
        }
    }
}
