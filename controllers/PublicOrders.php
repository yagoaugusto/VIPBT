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
        // Debug: Check if method is called
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
            try {
                $order = $this->orderModel->getOrderByPublicCode($publicCode);
                
                if($order){
                    $data['order'] = $order;
                    $data['order_items'] = $this->orderModel->getOrderItems($order->id);
                    
                    // Carrega informações de fulfillment
                    $data['fulfillment'] = $this->fulfillmentModel->getFulfillmentByOrderId($order->id);
                    
                    // Carrega informações de pagamentos
                    $data['payments'] = $this->paymentModel->getPaymentsByOrderId($order->id);
                    
                    // Gera timeline do pedido
                    $data['timeline'] = $this->generateOrderTimeline($order, $data['fulfillment'], $data['payments']);
                    
                } else {
                    $data['error'] = 'Pedido não encontrado. Verifique o código informado.';
                }
            } catch (Exception $e) {
                $data['error'] = 'Erro ao consultar pedido. Tente novamente.';
                error_log("Error in PublicOrders consultation: " . $e->getMessage());
            }
        }

        $this->view('public/order_consultation', $data, false); // false = sem layout padrão
    }

    private function generateOrderTimeline($order, $fulfillment, $payments){
        $timeline = [];
        
        // Data de criação do pedido
        $timeline[] = [
            'date' => $order->created_at,
            'status' => 'Pedido Criado',
            'description' => 'Seu pedido foi registrado em nosso sistema',
            'icon' => 'fa-shopping-cart',
            'color' => 'success'
        ];

        // Status de pagamento
        if(!empty($payments)){
            $totalPaid = array_sum(array_column($payments, 'valor'));
            if($totalPaid > 0){
                $paymentDate = min(array_column($payments, 'data'));
                $timeline[] = [
                    'date' => $paymentDate,
                    'status' => $totalPaid >= $order->total ? 'Pagamento Confirmado' : 'Pagamento Parcial',
                    'description' => 'Pagamento de R$ ' . number_format($totalPaid, 2, ',', '.') . ' confirmado',
                    'icon' => 'fa-credit-card',
                    'color' => $totalPaid >= $order->total ? 'success' : 'warning'
                ];
            }
        }

        // Status fiscal (se implementado)
        if(isset($order->status_fiscal) && $order->status_fiscal == 'faturado'){
            $timeline[] = [
                'date' => $order->data_faturamento ?? $order->created_at,
                'status' => 'Nota Fiscal Emitida',
                'description' => 'Nota fiscal do pedido foi emitida',
                'icon' => 'fa-file-text',
                'color' => 'info'
            ];
        }

        // Status de fulfillment
        if($fulfillment){
            if($fulfillment->status == 'preparando' || $fulfillment->status == 'enviado' || $fulfillment->status == 'entregue'){
                $timeline[] = [
                    'date' => $fulfillment->created_at,
                    'status' => 'Preparando Envio',
                    'description' => 'Seu pedido está sendo preparado para envio',
                    'icon' => 'fa-box',
                    'color' => 'info'
                ];
            }

            if($fulfillment->status == 'enviado' || $fulfillment->status == 'entregue'){
                $timeline[] = [
                    'date' => $fulfillment->enviado_em,
                    'status' => 'Enviado',
                    'description' => 'Pedido enviado' . ($fulfillment->codigo_rastreio ? ' - Código: ' . $fulfillment->codigo_rastreio : ''),
                    'icon' => 'fa-truck',
                    'color' => 'primary'
                ];
            }

            if($fulfillment->status == 'entregue'){
                $timeline[] = [
                    'date' => $fulfillment->entregue_em,
                    'status' => 'Entregue',
                    'description' => 'Pedido entregue com sucesso',
                    'icon' => 'fa-check-circle',
                    'color' => 'success'
                ];
            }
        }

        // Ordena por data
        usort($timeline, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $timeline;
    }
}