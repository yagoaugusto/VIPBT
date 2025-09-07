<?php

use core\Controller;
use core\Session;

class TradeIns extends Controller {
    private $tradeInModel;
    private $customerModel;
    private $userModel;
    private $brandModel;
    private $productModel;

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

        $this->tradeInModel = $this->model('TradeInModel');
        $this->customerModel = $this->model('Customer');
        $this->userModel = $this->model('UserModel');
        $this->brandModel = $this->model('Brand');
        $this->productModel = $this->model('Product');
    }

    public function index(){
        $tradeIns = $this->tradeInModel->getAllTradeIns();
        $data = [
            'title' => 'Avaliações de Trade-in',
            'tradeIns' => $tradeIns
        ];
        $this->view('tradeins/index', $data);
    }

    public function add(){
        if(!Session::isLoggedIn()){
            header('Location: ' . URL_ROOT . '/users/login');
            exit();
        }
        if(Session::get('user_perfil') != 'admin' && Session::get('user_perfil') != 'estoquista'){
            header('Location: ' . URL_ROOT);
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Não usar filter_input_array com $_POST diretamente, pois o body é FormData
            // Os dados são acessados diretamente de $_POST
            
            $items = json_decode($_POST['items'] ?? '[]', true); // Garante que seja um array, mesmo que vazio ou inválido

            $data = [
                'customer_id' => $_POST['customer_id'] ?? '',
                'status' => 'pendente', // Status inicial
                'avaliador_user_id' => Session::get('user_id'),
                'items' => $items,
                'customer_id_err' => ''
            ];

            if(empty($data['customer_id'])){
                $data['customer_id_err'] = 'Por favor, selecione o cliente.';
            }

            if(empty($data['customer_id_err'])){
                $tradeInId = $this->tradeInModel->addTradeIn($data);
                if($tradeInId){
                    // Não redirecionar, retornar JSON
                    echo json_encode(['success' => true, 'trade_in_id' => $tradeInId]);
                    exit();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Algo deu errado ao registrar o trade-in.']);
                    exit();
                }
            } else {
                // Se houver erros de validação, retornar JSON com os erros
                echo json_encode(['success' => false, 'message' => 'Erro de validação.', 'errors' => $data]);
                exit();
            }

        } else {
            $data = [
                'title' => 'Nova Avaliação de Trade-in',
                'customers' => $this->customerModel->getAllCustomers(),
                'brands' => $this->brandModel->getAllBrands(),
                'products' => $this->productModel->getAllProducts(),
                'customer_id' => '',
                'items' => [],
                'customer_id_err' => '' // Inicializa para a view
            ];
            $this->view('tradeins/add', $data);
        }
    }

    public function show($id){
        $tradeIn = $this->tradeInModel->getTradeInById($id);
        $tradeInItems = $this->tradeInModel->getTradeInItems($id);

        $data = [
            'title' => 'Detalhes da Avaliação de Trade-in',
            'tradeIn' => $tradeIn,
            'tradeInItems' => $tradeInItems
        ];
        $this->view('tradeins/show', $data);
    }
}
