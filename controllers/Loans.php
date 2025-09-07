<?php

use core\Controller;
use core\Session;

class Loans extends Controller {
    private $loanModel;
    private $customerModel;
    private $userModel;
    private $stockModel;

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

        $this->loanModel = $this->model('LoanModel');
        $this->customerModel = $this->model('Customer');
        $this->userModel = $this->model('UserModel');
        $this->stockModel = $this->model('StockModel');
    }

    public function index(){
        $loans = $this->loanModel->getAllLoans();
        $data = [
            'title' => 'Empréstimos de Teste',
            'loans' => $loans
        ];
        $this->view('loans/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Não usar filter_input_array com $_POST diretamente, pois o body é FormData e pode corromper o JSON

            $data = [
                'customer_id' => $_POST['customer_id'] ?? '',
                'vendedor_user_id' => Session::get('user_id'),
                'data_saida' => $_POST['data_saida'] ?? '',
                'data_prevista_retorno' => !empty($_POST['data_prevista_retorno']) ? $_POST['data_prevista_retorno'] : null,
                'observacoes' => trim($_POST['observacoes'] ?? ''),
                'items' => json_decode($_POST['items'] ?? '[]', true), // Itens enviados via JS
                'customer_id_err' => '',
                'data_saida_err' => '',
                'items_err' => ''
            ];

            // Validação
            if(empty($data['customer_id'])){
                $data['customer_id_err'] = 'Por favor, selecione o cliente.';
            }
            if(empty($data['data_saida'])){
                $data['data_saida_err'] = 'Por favor, insira a data de saída.';
            }
            if(empty($data['items']) || !is_array($data['items'])){
                $data['items_err'] = 'Por favor, adicione pelo menos um item para empréstimo.';
            }

            if(empty($data['customer_id_err']) && empty($data['data_saida_err']) && empty($data['items_err'])){
                $loanId = $this->loanModel->addLoan($data);
                if($loanId){
                    echo json_encode(['success' => true, 'loan_id' => $loanId]);
                    exit();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Algo deu errado ao registrar o empréstimo.']);
                    exit();
                }
            } else {
                // Se houver erros de validação, retornar JSON com os erros
                echo json_encode(['success' => false, 'message' => 'Erro de validação.', 'errors' => $data]);
                exit();
            }

        } else {
            $data = [
                'title' => 'Novo Empréstimo de Teste',
                'customers' => $this->customerModel->getAllCustomers(),
                'available_stock_items' => $this->stockModel->getAvailableStockItemsForLoan(),
                'customer_id' => '',
                'data_saida' => date('Y-m-d'),
                'data_prevista_retorno' => '',
                'observacoes' => '',
                'items' => [],
                'customer_id_err' => '',
                'data_saida_err' => '',
                'items_err' => ''
            ];
            $this->view('loans/add', $data);
        }
    }

    public function show($id){
        $loan = $this->loanModel->getLoanById($id);
        $loanItems = $this->loanModel->getLoanItems($id);

        $data = [
            'title' => 'Detalhes do Empréstimo',
            'loan' => $loan,
            'loanItems' => $loanItems
        ];
        $this->view('loans/show', $data);
    }

    public function returnItem($loan_id, $stock_item_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $estado_retorno = $_POST['estado_retorno'];

            if($this->loanModel->returnLoanItem($loan_id, $stock_item_id, $estado_retorno)){
                Session::flash('loan_message', 'Item devolvido com sucesso!');
                header('Location: ' . URL_ROOT . '/loans/show/' . $loan_id);
                exit();
            } else {
                die('Algo deu errado ao devolver o item.');
            }
        } else {
            header('Location: ' . URL_ROOT . '/loans/show/' . $loan_id);
            exit();
        }
    }
}