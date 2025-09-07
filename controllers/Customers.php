<?php

use core\Controller;

class Customers extends Controller {
    private $customerModel;
    private $productModel;

    public function __construct(){
        $this->customerModel = $this->model('Customer');
        $this->productModel = $this->model('Product');
    }

    public function index(){
        $customers = $this->customerModel->getAllCustomers();
        $data = [
            'title' => 'Clientes',
            'customers' => $customers
        ];
        $this->view('customers/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'nome' => trim($_POST['nome']),
                'telefone' => trim($_POST['telefone']),
                'cidade' => trim($_POST['cidade']),
                'raquete_entrada_bool' => isset($_POST['raquete_entrada_bool']) ? 1 : 0,
                'raquete_entrada_produto_id' => !empty($_POST['raquete_entrada_produto_id']) ? $_POST['raquete_entrada_produto_id'] : null,
                'nome_err' => ''
            ];

            if(empty($data['nome'])){
                $data['nome_err'] = 'Por favor, insira o nome do cliente.';
            }

            if(empty($data['nome_err'])){
                if($this->customerModel->addCustomer($data)){
                    header('Location: ' . URL_ROOT . '/customers');
                } else {
                    die('Algo deu errado.');
                }
            } else {
                $data['products'] = $this->productModel->getAllProducts();
                $this->view('customers/add', $data);
            }

        } else {
            $data = [
                'title' => 'Adicionar Cliente',
                'nome' => '',
                'telefone' => '',
                'cidade' => '',
                'raquete_entrada_bool' => 0,
                'raquete_entrada_produto_id' => null,
                'products' => $this->productModel->getAllProducts()
            ];
            $this->view('customers/add', $data);
        }
    }

    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'nome' => trim($_POST['nome']),
                'telefone' => trim($_POST['telefone']),
                'cidade' => trim($_POST['cidade']),
                'raquete_entrada_bool' => isset($_POST['raquete_entrada_bool']) ? 1 : 0,
                'raquete_entrada_produto_id' => !empty($_POST['raquete_entrada_produto_id']) ? $_POST['raquete_entrada_produto_id'] : null,
                'nome_err' => ''
            ];

            if(empty($data['nome'])){
                $data['nome_err'] = 'Por favor, insira o nome do cliente.';
            }

            if(empty($data['nome_err'])){
                if($this->customerModel->updateCustomer($data)){
                    header('Location: ' . URL_ROOT . '/customers');
                } else {
                    die('Algo deu errado.');
                }
            } else {
                $data['products'] = $this->productModel->getAllProducts();
                $this->view('customers/edit', $data);
            }

        } else {
            $customer = $this->customerModel->getCustomerById($id);

            $data = [
                'title' => 'Editar Cliente',
                'id' => $id,
                'nome' => $customer->nome,
                'telefone' => $customer->telefone,
                'cidade' => $customer->cidade,
                'raquete_entrada_bool' => $customer->raquete_entrada_bool,
                'raquete_entrada_produto_id' => $customer->raquete_entrada_produto_id,
                'products' => $this->productModel->getAllProducts()
            ];
            $this->view('customers/edit', $data);
        }
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->customerModel->deleteCustomer($id)){
                header('Location: ' . URL_ROOT . '/customers');
            } else {
                die('Algo deu errado.');
            }
        } else {
            header('Location: ' . URL_ROOT . '/customers');
        }
    }
}
