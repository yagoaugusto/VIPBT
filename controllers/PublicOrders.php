<?php

use core\Controller;

class PublicOrders extends Controller {
    private $orderModel;
    private $fulfillmentModel;
    private $paymentModel;

    public function __construct(){
        $this->orderModel = $this->model('OrderModel');
        $this->fulfillmentModel = $this->model('FulfillmentModel');
        $this->paymentModel = $this->model('PaymentModel');
    }

    // Página de consulta pública - não requer autenticação
    public function consulta($publicCode = null){
        // Debug
        error_log("PublicOrders::consulta called with code: " . ($publicCode ?? 'null'));

        $data = [
            'title' => 'Consulta de Pedido',
            'public_code' => $publicCode,
            'order' => null,
            'order_items' => [],
            'fulfillment' => null,
            'payments' => [],
            'timeline' => [],
            'error' => null,
            'verification_required' => false
        ];

        // Check if we have a code from GET parameter
        if(!$publicCode && isset($_GET['code'])){
            $publicCode = $_GET['code'];
            $data['public_code'] = $publicCode;
        }

        if($publicCode){
            // Busca o pedido pelo código público
            try {
                $order = $this->orderModel->getOrderByPublicCode($publicCode);
            } catch (Exception $e) {
                error_log("Error fetching order by public code: " . $e->getMessage());
                $order = null;
            }

            if($order){
                $data['order'] = $order;

                // Carrega itens do pedido (tolerante a erro)
                try {
                    $data['order_items'] = $this->orderModel->getOrderItems($order->id);
                } catch (Exception $e) {
                    error_log("Error fetching order items: " . $e->getMessage());
                    $data['order_items'] = [];
                }

                // Fulfillment (tolerante a erro)
                try {
                    $data['fulfillment'] = $this->fulfillmentModel->getFulfillmentByOrderId($order->id);
                } catch (Exception $e) {
                    error_log("Error fetching fulfillment: " . $e->getMessage());
                    $data['fulfillment'] = null;
                }

                // Pagamentos (tolerante a erro)
                try {
                    $data['payments'] = $this->paymentModel->getPaymentsByOrderId($order->id);
                } catch (Exception $e) {
                    error_log("Error fetching payments: " . $e->getMessage());
                    $data['payments'] = [];
                }

                // Timeline
                try {
                    $data['timeline'] = $this->generateOrderTimeline($order, $data['fulfillment'], $data['payments']);
                } catch (Exception $e) {
                    error_log("Error generating timeline: " . $e->getMessage());
                    $data['timeline'] = [];
                }
            } else {
                $data['error'] = 'Pedido não encontrado. Verifique o código informado.';
            }
        }

        $this->view('public/order_consultation', $data, false); // false = sem layout padrão
    }

    private function generateOrderTimeline($order, $fulfillment, $payments){
        $timeline = [];

        // Data de criação do pedido (fallback para campo 'data')
        $createdDate = $order->data ?? ($order->created_at ?? date('Y-m-d'));
        $timeline[] = [
            'date' => $createdDate,
            'status' => 'Pedido Criado',
            'description' => 'Seu pedido foi registrado em nosso sistema',
            'icon' => 'fa-shopping-cart',
            'color' => 'success'
        ];

        // Status de pagamento
        if(is_array($payments) && !empty($payments)){
            $totalPaid = 0;
            $dates = [];
            foreach ($payments as $p) {
                $totalPaid += (float)($p->valor ?? 0);
                if (!empty($p->data)) { $dates[] = $p->data; }
            }
            if($totalPaid > 0){
                $paymentDate = !empty($dates) ? min($dates) : $createdDate;
                $timeline[] = [
                    'date' => $paymentDate,
                    'status' => $totalPaid >= (float)($order->total ?? 0) ? 'Pagamento Confirmado' : 'Pagamento Parcial',
                    'description' => 'Pagamento de R$ ' . number_format($totalPaid, 2, ',', '.') . ' confirmado',
                    'icon' => 'fa-credit-card',
                    'color' => $totalPaid >= (float)($order->total ?? 0) ? 'success' : 'warning'
                ];
            }
        }

        // Status fiscal (se implementado)
        if(isset($order->status_fiscal) && $order->status_fiscal == 'faturado'){
            $timeline[] = [
                'date' => $order->data_faturamento ?? $createdDate,
                'status' => 'Nota Fiscal Emitida',
                'description' => 'Nota fiscal do pedido foi emitida',
                'icon' => 'fa-file-text',
                'color' => 'info'
            ];
        }

        // Status de fulfillment
        if($fulfillment){
            if(in_array($fulfillment->status, ['preparando','enviado','entregue'], true)){
                $timeline[] = [
                    'date' => $fulfillment->created_at ?? $createdDate,
                    'status' => 'Preparando Envio',
                    'description' => 'Seu pedido está sendo preparado para envio',
                    'icon' => 'fa-box',
                    'color' => 'info'
                ];
            }

            if(in_array($fulfillment->status, ['enviado','entregue'], true)){
                $timeline[] = [
                    'date' => $fulfillment->enviado_em ?? $fulfillment->created_at ?? $createdDate,
                    'status' => 'Enviado',
                    'description' => 'Pedido enviado' . (!empty($fulfillment->codigo_rastreio) ? ' - Código: ' . $fulfillment->codigo_rastreio : ''),
                    'icon' => 'fa-truck',
                    'color' => 'primary'
                ];
            }

            if($fulfillment->status === 'entregue'){
                $timeline[] = [
                    'date' => $fulfillment->entregue_em ?? $fulfillment->enviado_em ?? $createdDate,
                    'status' => 'Entregue',
                    'description' => 'Pedido entregue com sucesso',
                    'icon' => 'fa-check-circle',
                    'color' => 'success'
                ];
            }
        }

        // Ordena por data com segurança
        usort($timeline, function($a, $b) {
            $ad = isset($a['date']) ? strtotime($a['date']) : 0;
            $bd = isset($b['date']) ? strtotime($b['date']) : 0;
            return ($ad <=> $bd);
        });

        return $timeline;
    }
}