<?php

use core\Database;

class FinancialIndicatorsModel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getFinancialOverview($start_date = null, $end_date = null) {
        // Build date filter
        $dateFilter = "";
        $params = [];
        
        if ($start_date) {
            $dateFilter .= " AND o.data >= :start_date";
            $params[':start_date'] = $start_date;
        }
        
        if ($end_date) {
            $dateFilter .= " AND o.data <= :end_date";
            $params[':end_date'] = $end_date;
        }

        // Observação: lucratividade considera valor recebido menos custo real de aquisição dos itens vendidos
        $this->db->query("
            SELECT 
                COUNT(DISTINCT o.id) as total_orders,
                COUNT(DISTINCT CASE WHEN o.status_pedido = 'faturado' OR o.status_pedido = 'vendido' THEN o.id END) as total_sales,
                COALESCE(SUM(CASE WHEN o.status_pedido = 'faturado' OR o.status_pedido = 'vendido' THEN oi.qtd * oi.preco_unit - oi.desconto END), 0) as total_revenue,
                COALESCE(SUM(r.valor_recebido), 0) as amount_received,
                COALESCE(SUM(r.valor_a_receber), 0) as amount_to_receive,
                COALESCE(SUM(CASE WHEN (o.status_pedido = 'faturado' OR o.status_pedido = 'vendido') 
                    THEN (
                        CASE 
                            WHEN oi.stock_item_id IS NOT NULL THEN oi.qtd * COALESCE(si.aquisicao_custo, 0)
                            ELSE oi.qtd * COALESCE(pp.custo, 0)
                        END
                    ) END), 0) as total_cost,
                COALESCE(
                    SUM(r.valor_recebido) - 
                    SUM(CASE WHEN (o.status_pedido = 'faturado' OR o.status_pedido = 'vendido') 
                        THEN (
                            CASE 
                                WHEN oi.stock_item_id IS NOT NULL THEN oi.qtd * COALESCE(si.aquisicao_custo, 0)
                                ELSE oi.qtd * COALESCE(pp.custo, 0)
                            END
                        ) END)
                , 0) as profit
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN receivables r ON o.id = r.order_id
            LEFT JOIN stock_items si ON oi.stock_item_id = si.id
            LEFT JOIN product_prices pp ON pp.product_id = oi.product_id 
                AND pp.vigente_desde = (
                    SELECT MAX(pp2.vigente_desde) 
                    FROM product_prices pp2 
                    WHERE pp2.product_id = oi.product_id 
                      AND pp2.vigente_desde <= o.data
                )
            WHERE 1=1 {$dateFilter}
        ");
        
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        $row = $this->db->single();
        // Calcula margem de lucro baseada no valor recebido, não na receita total
        if ($row) {
            $amountReceived = isset($row->amount_received) ? (float)$row->amount_received : 0.0;
            $profit = isset($row->profit) ? (float)$row->profit : 0.0;
            $row->profit_margin = $amountReceived > 0 ? ($profit / $amountReceived) : 0.0;
        }
        return $row;
    }

    public function getMostSoldProducts($start_date = null, $end_date = null, $limit = 10) {
        $dateFilter = "";
        $params = [];
        
        if ($start_date) {
            $dateFilter .= " AND o.data >= :start_date";
            $params[':start_date'] = $start_date;
        }
        
        if ($end_date) {
            $dateFilter .= " AND o.data <= :end_date";
            $params[':end_date'] = $end_date;
        }

        $this->db->query("
            SELECT 
                p.nome as product_name,
                p.sku,
                SUM(oi.qtd) as total_quantity,
                SUM(oi.qtd * oi.preco_unit - oi.desconto) as total_value,
                AVG(oi.preco_unit) as avg_price
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN products p ON oi.product_id = p.id
            WHERE (o.status_pedido = 'faturado' OR o.status_pedido = 'vendido') {$dateFilter}
            GROUP BY p.id, p.nome, p.sku
            ORDER BY total_quantity DESC
            LIMIT {$limit}
        ");
        
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        return $this->db->resultSet();
    }

    public function getTopCustomersByPurchases($start_date = null, $end_date = null, $limit = 10) {
        $dateFilter = "";
        $params = [];
        
        if ($start_date) {
            $dateFilter .= " AND o.data >= :start_date";
            $params[':start_date'] = $start_date;
        }
        
        if ($end_date) {
            $dateFilter .= " AND o.data <= :end_date";
            $params[':end_date'] = $end_date;
        }

        $this->db->query("
            SELECT 
                c.nome as customer_name,
                c.telefone,
                COUNT(DISTINCT o.id) as total_orders,
                SUM(oi.qtd * oi.preco_unit - oi.desconto) as total_spent,
                AVG(oi.qtd * oi.preco_unit - oi.desconto) as avg_order_value
            FROM customers c
            JOIN orders o ON c.id = o.customer_id
            JOIN order_items oi ON o.id = oi.order_id
            WHERE (o.status_pedido = 'faturado' OR o.status_pedido = 'vendido') {$dateFilter}
            GROUP BY c.id, c.nome, c.telefone
            ORDER BY total_spent DESC
            LIMIT {$limit}
        ");
        
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        return $this->db->resultSet();
    }

    public function getTopCustomersByLoans($start_date = null, $end_date = null, $limit = 10) {
        $dateFilter = "";
        $params = [];
        
        if ($start_date) {
            $dateFilter .= " AND l.data_saida >= :start_date";
            $params[':start_date'] = $start_date;
        }
        
        if ($end_date) {
            $dateFilter .= " AND l.data_saida <= :end_date";
            $params[':end_date'] = $end_date;
        }

        $this->db->query("
            SELECT 
                c.nome as customer_name,
                c.telefone,
                COUNT(DISTINCT l.id) as total_loans,
                COUNT(li.id) as total_items_borrowed,
                COUNT(CASE WHEN l.status = 'convertido_em_venda' THEN l.id END) as loans_converted_to_sales
            FROM customers c
            JOIN loans l ON c.id = l.customer_id
            LEFT JOIN loan_items li ON l.id = li.loan_id
            WHERE 1=1 {$dateFilter}
            GROUP BY c.id, c.nome, c.telefone
            ORDER BY total_loans DESC, total_items_borrowed DESC
            LIMIT {$limit}
        ");
        
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        return $this->db->resultSet();
    }

    public function getSalesChannelStats($start_date = null, $end_date = null) {
        $dateFilter = "";
        $params = [];
        
        if ($start_date) {
            $dateFilter .= " AND o.data >= :start_date";
            $params[':start_date'] = $start_date;
        }
        
        if ($end_date) {
            $dateFilter .= " AND o.data <= :end_date";
            $params[':end_date'] = $end_date;
        }

        $this->db->query("
            SELECT 
                ch.nome as channel_name,
                COUNT(DISTINCT o.id) as total_orders,
                COUNT(DISTINCT CASE WHEN o.status_pedido = 'faturado' OR o.status_pedido = 'vendido' THEN o.id END) as total_sales,
                COALESCE(SUM(CASE WHEN o.status_pedido = 'faturado' OR o.status_pedido = 'vendido' THEN oi.qtd * oi.preco_unit - oi.desconto END), 0) as total_revenue
            FROM channels ch
            LEFT JOIN orders o ON ch.id = o.channel_id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE ch.ativo = 1 {$dateFilter}
            GROUP BY ch.id, ch.nome
            ORDER BY total_revenue DESC
        ");
        
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        return $this->db->resultSet();
    }

    public function getPaymentMethodStats($start_date = null, $end_date = null) {
        $dateFilter = "";
        $params = [];
        
        if ($start_date) {
            $dateFilter .= " AND p.data >= :start_date";
            $params[':start_date'] = $start_date;
        }
        
        if ($end_date) {
            $dateFilter .= " AND p.data <= :end_date";
            $params[':end_date'] = $end_date;
        }

        $this->db->query("
            SELECT 
                p.forma as payment_method,
                COUNT(p.id) as total_payments,
                SUM(p.valor) as total_amount,
                AVG(p.valor) as avg_amount
            FROM payments p
            WHERE p.status_pagamento = 'pago' {$dateFilter}
            GROUP BY p.forma
            ORDER BY total_amount DESC
        ");
        
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        return $this->db->resultSet();
    }

    public function getMonthlyRevenueData($start_date = null, $end_date = null) {
        $dateFilter = "";
        $params = [];
        
        if ($start_date) {
            $dateFilter .= " AND o.data >= :start_date";
            $params[':start_date'] = $start_date;
        }
        
        if ($end_date) {
            $dateFilter .= " AND o.data <= :end_date";
            $params[':end_date'] = $end_date;
        }

        $this->db->query("
            SELECT 
                DATE_FORMAT(o.data, '%Y-%m') as month,
                COUNT(DISTINCT o.id) as total_orders,
                COUNT(DISTINCT CASE WHEN o.status_pedido = 'faturado' OR o.status_pedido = 'vendido' THEN o.id END) as total_sales,
                COALESCE(SUM(CASE WHEN o.status_pedido = 'faturado' OR o.status_pedido = 'vendido' THEN oi.qtd * oi.preco_unit - oi.desconto END), 0) as revenue
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE 1=1 {$dateFilter}
            GROUP BY DATE_FORMAT(o.data, '%Y-%m')
            ORDER BY month DESC
            LIMIT 12
        ");
        
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        return $this->db->resultSet();
    }

    public function getSellerStats($start_date = null, $end_date = null) {
        $dateFilter = "";
        $params = [];
        if ($start_date) {
            $dateFilter .= " AND o.data >= :start_date";
            $params[':start_date'] = $start_date;
        }
        if ($end_date) {
            $dateFilter .= " AND o.data <= :end_date";
            $params[':end_date'] = $end_date;
        }

        // Estatísticas por vendedor considerando valor recebido e custo real de aquisição
        $this->db->query("
            SELECT
                u.id as user_id,
                u.nome as seller_name,
                COUNT(DISTINCT CASE WHEN (o.status_pedido = 'faturado' OR o.status_pedido = 'vendido') THEN o.id END) as total_sales,
                COALESCE(SUM(CASE WHEN (o.status_pedido = 'faturado' OR o.status_pedido = 'vendido') THEN (oi.qtd * oi.preco_unit - oi.desconto) END), 0) as total_revenue,
                COALESCE(SUM(CASE WHEN (o.status_pedido = 'faturado' OR o.status_pedido = 'vendido') 
                    THEN (
                        CASE 
                            WHEN oi.stock_item_id IS NOT NULL THEN oi.qtd * COALESCE(si.aquisicao_custo, 0)
                            ELSE oi.qtd * COALESCE(pp.custo, 0)
                        END
                    ) END), 0) as total_cost,
                COALESCE(SUM(r.valor_recebido), 0) as amount_received,
                COALESCE(
                    SUM(r.valor_recebido) -
                    SUM(CASE WHEN (o.status_pedido = 'faturado' OR o.status_pedido = 'vendido') 
                        THEN (
                            CASE 
                                WHEN oi.stock_item_id IS NOT NULL THEN oi.qtd * COALESCE(si.aquisicao_custo, 0)
                                ELSE oi.qtd * COALESCE(pp.custo, 0)
                            END
                        ) END)
                , 0) as profit
            FROM sellers s
            JOIN users u ON u.id = s.user_id AND u.ativo = 1
            LEFT JOIN orders o ON o.seller_id = s.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN receivables r ON o.id = r.order_id
            LEFT JOIN stock_items si ON oi.stock_item_id = si.id
            LEFT JOIN product_prices pp ON pp.product_id = oi.product_id
                AND pp.vigente_desde = (
                    SELECT MAX(pp2.vigente_desde) FROM product_prices pp2
                    WHERE pp2.product_id = oi.product_id AND pp2.vigente_desde <= o.data
                )
            WHERE 1=1 {$dateFilter}
            GROUP BY u.id, u.nome
            ORDER BY total_revenue DESC
        ");

        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->resultSet();
    }
}