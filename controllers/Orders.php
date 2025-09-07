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

            $data = [
                'customer_id' => $requestData['customer_id'],
                'seller_id' => $requestData['seller_id'],
                'channel_id' => $requestData['channel_id'],
                'data' => date('Y-m-d'),
                'observacao' => $requestData['observacao'],
                'items' => $requestData['items'],
                'tradeins' => $requestData['tradeins'] ?? [],
                'total_credits' => $requestData['total_credits'] ?? 0
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
}
