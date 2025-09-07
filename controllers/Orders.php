<?php

use core\Controller;

class Orders extends Controller {
    private $orderModel;
    private $customerModel;
    private $sellerModel;
    private $channelModel;
    private $productModel;

    public function __construct(){
        $this->orderModel = $this->model('OrderModel');
        $this->customerModel = $this->model('Customer');
        $this->sellerModel = $this->model('SellerModel');
        $this->channelModel = $this->model('Channel');
        $this->productModel = $this->model('Product');
    }

    public function index(){
        $orders = $this->orderModel->getAllOrders();
        $data = [
            'title' => 'Pedidos de Venda',
            'orders' => $orders
        ];
        $this->view('orders/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // O processamento do formulário de pedido é complexo e será implementado com JS no frontend.
            // Por enquanto, vamos apenas preparar os dados para o formulário.
            // A lógica de inserção será chamada via AJAX/Fetch.
            
            // Decodifica o JSON enviado pelo frontend
            $json = file_get_contents('php://input');
            $requestData = json_decode($json, true);

            // Validate JSON decode
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($requestData)) {
                echo json_encode(['success' => false, 'message' => 'Dados inválidos enviados.']);
                exit();
            }

            // Validate required fields
            if (empty($requestData['customer_id']) || !is_numeric($requestData['customer_id'])) {
                echo json_encode(['success' => false, 'message' => 'Cliente é obrigatório.']);
                exit();
            }

            if (empty($requestData['seller_id']) || !is_numeric($requestData['seller_id'])) {
                echo json_encode(['success' => false, 'message' => 'Vendedor é obrigatório.']);
                exit();
            }

            if (empty($requestData['channel_id']) || !is_numeric($requestData['channel_id'])) {
                echo json_encode(['success' => false, 'message' => 'Canal de venda é obrigatório.']);
                exit();
            }

            if (empty($requestData['items']) || !is_array($requestData['items']) || count($requestData['items']) === 0) {
                echo json_encode(['success' => false, 'message' => 'Pelo menos um item deve ser adicionado ao pedido.']);
                exit();
            }

            $data = [
                'customer_id' => (int)$requestData['customer_id'],
                'seller_id' => (int)$requestData['seller_id'],
                'channel_id' => (int)$requestData['channel_id'],
                'data' => date('Y-m-d'),
                'observacao' => $requestData['observacao'] ?? '',
                'items' => $requestData['items'],
                'tradeins' => $requestData['tradeins'] ?? [],
                'total_credits' => (float)($requestData['total_credits'] ?? 0)
            ];

            try {
                $orderId = $this->orderModel->addOrder($data);

                if($orderId){
                    echo json_encode(['success' => true, 'order_id' => $orderId]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao criar o pedido.']);
                }
            } catch (Exception $e) {
                // Retorna erro específico, especialmente para problemas de estoque
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            // Impede a renderização da view
            exit();

        } else {
            $data = [
                'title' => 'Novo Pedido',
                'customers' => $this->customerModel->getAllCustomers(),
                'sellers' => $this->sellerModel->getAllSellers(),
                'channels' => $this->channelModel->getAllChannels(),
                'products' => $this->productModel->getAllProducts()
            ];
            $this->view('orders/add', $data);
        }
    }

    public function show($id){
        $order = $this->orderModel->getOrderById($id);
        
        // Verifica se o pedido existe
        if (!$order) {
            // Redireciona para a lista de pedidos com mensagem de erro
            header('Location: ' . URL_ROOT . '/orders');
            exit();
        }
        
        $items = $this->orderModel->getOrderItems($id);
        $fulfillments = $this->orderModel->getOrderFulfillments($id);
        
        // Load credits applied to this order
        $credits = $this->orderModel->getOrderCredits($id);

        $data = [
            'title' => 'Detalhes do Pedido ' . $order->public_code,
            'order' => $order,
            'items' => $items,
            'fulfillments' => $fulfillments,
            'credits' => $credits
        ];
        $this->view('orders/show', $data);
    }

    public function updateFiscalStatus($order_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            $status = $data['status'] ?? '';
            
            if(empty($status) || !in_array($status, ['nao_faturado', 'faturado'])){
                echo json_encode(['success' => false, 'message' => 'Status inválido']);
                exit();
            }

            try {
                $result = $this->orderModel->updateOrderFiscalStatus($order_id, $status);
                if($result){
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }
    }

    public function updateDeliveryStatus($order_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            $status = $data['status'] ?? '';
            
            if(empty($status) || !in_array($status, ['nao_entregue', 'preparando', 'enviado', 'entregue', 'entrega_parcial'])){
                echo json_encode(['success' => false, 'message' => 'Status inválido']);
                exit();
            }

            try {
                $result = $this->orderModel->updateOrderDeliveryStatus($order_id, $status);
                if($result){
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }
    }

    public function confirmSale($order_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Simula usuário logado - em um sistema real, isso viria da sessão
            $current_user_id = 1; // TODO: Implementar sistema de autenticação
            
            try {
                $result = $this->orderModel->confirmOrderAsSale($order_id, $current_user_id);
                if($result){
                    echo json_encode(['success' => true, 'message' => 'Pedido confirmado como venda com sucesso!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao confirmar venda']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }
    }

    public function updateOrderStatus($order_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            $status = $data['status'] ?? '';
            $current_user_id = 1; // TODO: Implementar sistema de autenticação
            
            if(empty($status) || !in_array($status, ['novo', 'confirmado', 'vendido', 'cancelado'])){
                echo json_encode(['success' => false, 'message' => 'Status inválido']);
                exit();
            }

            try {
                $result = $this->orderModel->updateOrderStatus($order_id, $status, $current_user_id);
                if($result){
                    echo json_encode(['success' => true, 'message' => 'Status do pedido atualizado com sucesso!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
        }
    }

    // Endpoint para estatísticas de conversão (para futuros relatórios)
    public function conversionStats(){
        $start_date = $_GET['start_date'] ?? null;
        $end_date = $_GET['end_date'] ?? null;
        $channel_id = $_GET['channel_id'] ?? null;
        $seller_id = $_GET['seller_id'] ?? null;

        try {
            $stats = $this->orderModel->getOrderConversionStats($start_date, $end_date, $channel_id, $seller_id);
            $stats_by_channel = $this->orderModel->getConversionStatsByChannel($start_date, $end_date);
            $stats_by_seller = $this->orderModel->getConversionStatsBySeller($start_date, $end_date);

            $data = [
                'title' => 'Estatísticas de Conversão de Pedidos',
                'overall_stats' => $stats,
                'stats_by_channel' => $stats_by_channel,
                'stats_by_seller' => $stats_by_seller,
                'filters' => [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'channel_id' => $channel_id,
                    'seller_id' => $seller_id
                ]
            ];

            // Se for uma requisição AJAX, retorna JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($data);
                exit();
            }

            // Carrega dados adicionais para filtros
            $data['channels'] = $this->channelModel->getAllChannels();
            $data['sellers'] = $this->sellerModel->getAllSellers();

            $this->view('orders/conversion_stats', $data);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit();
        }
    }
}
