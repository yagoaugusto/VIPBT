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
        $totals = $this->tradeInModel->getTradeInTotals($id);

        $data = [
            'title' => 'Detalhes da Avaliação de Trade-in',
            'tradeIn' => $tradeIn,
            'tradeInItems' => $tradeInItems,
            'totals' => $totals
        ];
        $this->view('tradeins/show', $data);
    }

    public function getApprovedByCustomer($customer_id){
        // API endpoint para buscar trade-ins aprovados por cliente
        if(!Session::isLoggedIn()){
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            exit();
        }

        try {
            $tradeins = $this->tradeInModel->getApprovedTradeInsByCustomer($customer_id);
            echo json_encode(['success' => true, 'tradeins' => $tradeins]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao buscar trade-ins']);
        }
        exit();
    }

    public function updateStatus($id){
        if(!Session::isLoggedIn()){
            header('Location: ' . URL_ROOT . '/users/login');
            exit();
        }
        
        // Apenas admin e financeiro podem aprovar/reprovar trade-ins
        if(Session::get('user_perfil') != 'admin' && Session::get('user_perfil') != 'financeiro'){
            Session::flash('error_message', 'Acesso negado. Apenas administradores e usuários do financeiro podem aprovar/reprovar avaliações.');
            header('Location: ' . URL_ROOT . '/tradeins/show/' . $id);
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $status = $_POST['status'] ?? '';
            $observacoes = $_POST['observacoes'] ?? '';
            
            // Validação do status
            $validStatuses = ['aprovado', 'reprovado'];
            if(!in_array($status, $validStatuses)){
                Session::flash('error_message', 'Status inválido.');
                header('Location: ' . URL_ROOT . '/tradeins/show/' . $id);
                exit();
            }

            // Atualiza o status do trade-in
            if($this->tradeInModel->updateTradeInStatus($id, $status, null, $observacoes)){
                $message = $status == 'aprovado' ? 'Trade-in aprovado com sucesso!' : 'Trade-in reprovado.';
                Session::flash('success_message', $message);
            } else {
                Session::flash('error_message', 'Erro ao atualizar status do trade-in.');
            }
        }

        header('Location: ' . URL_ROOT . '/tradeins/show/' . $id);
        exit();
    }
}
