<?php

use core\Controller;
use core\Session;

class Financial extends Controller {
    private $paymentModel;
    private $receivableModel;
    private $commissionModel;
    private $orderModel;
    private $financialIndicatorsModel;

    public function __construct(){
        // Apenas para usuários logados
        if(!Session::isLoggedIn()){
            header('Location: ' . URL_ROOT . '/users/login');
            exit();
        }
        // Apenas para admin ou perfis financeiros
        if(Session::get('user_perfil') != 'admin' && Session::get('user_perfil') != 'financeiro'){
            // Redirecionar para uma página de acesso negado ou home
            header('Location: ' . URL_ROOT);
            exit();
        }

        $this->paymentModel = $this->model('PaymentModel');
        $this->receivableModel = $this->model('ReceivableModel');
        
        $this->orderModel = $this->model('OrderModel');
        $this->financialIndicatorsModel = $this->model('FinancialIndicatorsModel');
    }

    public function indicators(){
        // Get filter parameters
        $start_date = $_GET['start_date'] ?? null;
        $end_date = $_GET['end_date'] ?? null;
        
        // Get dashboard data
        $overview = $this->financialIndicatorsModel->getFinancialOverview($start_date, $end_date);
        $mostSoldProducts = $this->financialIndicatorsModel->getMostSoldProducts($start_date, $end_date, 10);
        $topCustomersByPurchases = $this->financialIndicatorsModel->getTopCustomersByPurchases($start_date, $end_date, 10);
        $topCustomersByLoans = $this->financialIndicatorsModel->getTopCustomersByLoans($start_date, $end_date, 10);
        $salesChannelStats = $this->financialIndicatorsModel->getSalesChannelStats($start_date, $end_date);
        $paymentMethodStats = $this->financialIndicatorsModel->getPaymentMethodStats($start_date, $end_date);
        $monthlyRevenueData = $this->financialIndicatorsModel->getMonthlyRevenueData($start_date, $end_date);

        $data = [
            'title' => 'Indicadores Financeiros',
            'overview' => $overview,
            'most_sold_products' => $mostSoldProducts,
            'top_customers_by_purchases' => $topCustomersByPurchases,
            'top_customers_by_loans' => $topCustomersByLoans,
            'sales_channel_stats' => $salesChannelStats,
            'payment_method_stats' => $paymentMethodStats,
            'monthly_revenue_data' => $monthlyRevenueData,
            'filters' => [
                'start_date' => $start_date,
                'end_date' => $end_date
            ]
        ];

        $this->view('financial/indicators', $data);
    }

    public function receivables(){
        $receivables = $this->receivableModel->getAllReceivables();
        $data = [
            'title' => 'Contas a Receber',
            'receivables' => $receivables
        ];
        $this->view('financial/receivables', $data);
    }

    

    public function addPayment($order_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'order_id' => $order_id,
                'forma' => $_POST['forma'],
                'valor' => trim($_POST['valor']),
                'data' => trim($_POST['data']),
                'status_pagamento' => $_POST['status_pagamento'],
                'valor_err' => '',
                'data_err' => ''
            ];

            // Validação
            if(empty($data['valor']) || !is_numeric($data['valor']) || $data['valor'] <= 0){
                $data['valor_err'] = 'Por favor, insira um valor válido.';
            }
            if(empty($data['data'])){
                $data['data_err'] = 'Por favor, insira a data do pagamento.';
            }

            if(empty($data['valor_err']) && empty($data['data_err'])){
                if($this->paymentModel->addPayment($data)){
                    Session::flash('payment_message', 'Pagamento registrado com sucesso!');
                    header('Location: ' . URL_ROOT . '/orders/show/' . $order_id);
                } else {
                    die('Algo deu errado ao registrar o pagamento.');
                }
            } else {
                // Recarrega a view com erros
                $order = $this->orderModel->getOrderById($order_id);
                $payments = $this->paymentModel->getPaymentsByOrderId($order_id);
                $receivable = $this->receivableModel->getReceivableByOrderId($order_id);

                $data['order'] = $order;
                $data['payments'] = $payments;
                $data['receivable'] = $receivable;
                $data['title'] = 'Registrar Pagamento para Pedido ' . $order->public_code;
                $this->view('financial/addPayment', $data);
            }

        } else {
            $order = $this->orderModel->getOrderById($order_id);
            $payments = $this->paymentModel->getPaymentsByOrderId($order_id);
            $receivable = $this->receivableModel->getReceivableByOrderId($order_id);

            $data = [
                'title' => 'Registrar Pagamento para Pedido ' . $order->public_code,
                'order' => $order,
                'payments' => $payments,
                'receivable' => $receivable,
                'forma' => 'dinheiro',
                'valor' => $receivable->valor_a_receber, // Valor sugerido
                'data' => date('Y-m-d'),
                'status_pagamento' => 'pago'
            ];
            $this->view('financial/addPayment', $data);
        }
    }
}
