<?php

use core\Controller;
use core\Session;

class Logistics extends Controller {
    private $fulfillmentModel;
    private $orderModel;

    public function __construct(){
        // Apenas para usuários logados
        if(!Session::isLoggedIn()){
            header('Location: ' . URL_ROOT . '/users/login');
            exit();
        }
        // Apenas para admin ou estoquista
        if(Session::get('user_perfil') != 'admin' && Session::get('user_perfil') != 'estoquista'){
            header('Location: ' . URL_ROOT);
            exit();
        }

        $this->fulfillmentModel = $this->model('FulfillmentModel');
        $this->orderModel = $this->model('OrderModel');
    }

    public function index(){
        // Redireciona para a lista de pedidos, onde a logística será gerenciada
        header('Location: ' . URL_ROOT . '/orders');
        exit();
    }

    public function addFulfillment($order_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'order_id' => $order_id,
                'status' => $_POST['status'],
                'transportadora' => trim($_POST['transportadora']),
                'codigo_rastreio' => trim($_POST['codigo_rastreio']),
                'enviado_em' => $_POST['enviado_em'],
                'entregue_em' => $_POST['entregue_em'],
                'observacoes' => trim($_POST['observacoes']),
                'status_err' => '',
                'enviado_em_err' => ''
            ];

            // Validação
            if(empty($data['status'])){
                $data['status_err'] = 'Por favor, selecione o status.';
            }
            if($data['status'] == 'enviado' && empty($data['enviado_em'])){
                $data['enviado_em_err'] = 'Por favor, insira a data de envio.';
            }
            if($data['status'] == 'entregue' && empty($data['entregue_em'])){
                $data['entregue_em_err'] = 'Por favor, insira a data de entrega.';
            }

            if(empty($data['status_err']) && empty($data['enviado_em_err']) && empty($data['entregue_em_err'])){
                if($this->fulfillmentModel->addFulfillment($data)){
                    Session::flash('fulfillment_message', 'Registro de expedição adicionado com sucesso!');
                    header('Location: ' . URL_ROOT . '/orders/show/' . $order_id);
                } else {
                    die('Algo deu errado ao registrar a expedição.');
                }
            } else {
                // Recarrega a view com erros
                $order = $this->orderModel->getOrderById($order_id);
                $fulfillments = $this->fulfillmentModel->getFulfillmentsByOrderId($order_id);

                $data['order'] = $order;
                $data['fulfillments'] = $fulfillments;
                $data['title'] = 'Registrar Expedição para Pedido ' . $order->public_code;
                $this->view('logistics/addFulfillment', $data);
            }

        } else {
            $order = $this->orderModel->getOrderById($order_id);
            $fulfillments = $this->fulfillmentModel->getFulfillmentsByOrderId($order_id);

            $data = [
                'title' => 'Registrar Expedição para Pedido ' . $order->public_code,
                'order' => $order,
                'fulfillments' => $fulfillments,
                'status' => 'preparando',
                'transportadora' => '',
                'codigo_rastreio' => '',
                'enviado_em' => '',
                'entregue_em' => '',
                'observacoes' => ''
            ];
            $this->view('logistics/addFulfillment', $data);
        }
    }
}
