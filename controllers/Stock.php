<?php

use core\Controller;

class Stock extends Controller {
    private $stockModel;

    public function __construct(){
        $this->stockModel = $this->model('StockModel');
    }

    public function index(){
        $stockItems = $this->stockModel->getAllStockItems();
        $data = [
            'title' => 'Posição de Estoque',
            'stockItems' => $stockItems
        ];
        $this->view('stock/index', $data);
    }

    public function add(){
        // Apenas para usuários logados
        if(!core\Session::isLoggedIn()){
            header('Location: ' . URL_ROOT . '/users/login');
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'product_id' => $_POST['product_id'],
                'qtd' => trim($_POST['qtd']),
                'custo' => trim($_POST['custo']),
                'observacao' => trim($_POST['observacao']),
                'product_id_err' => '',
                'qtd_err' => '',
                'custo_err' => ''
            ];

            // Validação
            if(empty($data['product_id'])){
                $data['product_id_err'] = 'Por favor, selecione um produto.';
            }
            if(empty($data['qtd']) || $data['qtd'] <= 0){
                $data['qtd_err'] = 'Por favor, insira uma quantidade válida.';
            }
            if(!is_numeric($data['custo'])){
                $data['custo_err'] = 'Por favor, insira um custo válido.';
            }

            if(empty($data['product_id_err']) && empty($data['qtd_err']) && empty($data['custo_err'])){
                if($this->stockModel->addStockMovement($data)){
                    core\Session::flash('stock_message', 'Entrada de estoque registrada com sucesso!');
                    header('Location: ' . URL_ROOT . '/stock');
                } else {
                    die('Algo deu errado ao registrar a entrada de estoque.');
                }
            } else {
                // Carrega a view com erros
                $productModel = $this->model('Product');
                $data['products'] = $productModel->getAllProducts();
                $data['title'] = 'Entrada de Estoque';
                $this->view('stock/add', $data);
            }

        } else {
            $productModel = $this->model('Product');
            $data = [
                'title' => 'Entrada de Estoque',
                'products' => $productModel->getAllProducts(),
                'product_id' => '',
                'qtd' => '',
                'custo' => '',
                'observacao' => ''
            ];
            $this->view('stock/add', $data);
        }
    }
}
