<?php

use core\Controller;

class Stock extends Controller {
    private $stockModel;

    public function __construct(){
        $this->stockModel = $this->model('StockModel');
    }

    public function index(){
        $q = isset($_GET['q']) ? trim($_GET['q']) : null;
        $stockItems = $this->stockModel->getStockItems($q);
        $data = [
            'title' => 'Posição de Estoque',
            'stockItems' => $stockItems,
            'q' => $q
        ];
        $this->view('stock/index', $data);
    }

    public function balances(){
        $stockBalances = $this->stockModel->getStockBalances();
        $data = [
            'title' => 'Saldos de Estoque',
            'stockBalances' => $stockBalances
        ];
        $this->view('stock/balances', $data);
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
                'preco_venda' => isset($_POST['preco_venda']) ? trim($_POST['preco_venda']) : null,
                'observacao' => trim($_POST['observacao']),
                'product_id_err' => '',
                'qtd_err' => '',
                'custo_err' => '',
                'preco_venda_err' => '',
                'general_err' => ''
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
            if($data['preco_venda'] !== null && $data['preco_venda'] !== '' && !is_numeric($data['preco_venda'])){
                $data['preco_venda_err'] = 'Por favor, insira um preço de venda válido.';
            }

            if(empty($data['product_id_err']) && empty($data['qtd_err']) && empty($data['custo_err']) && empty($data['preco_venda_err'])){
                try {
                    if($this->stockModel->addStockMovement($data)){
                        core\Session::flash('stock_message', 'Entrada de estoque registrada com sucesso!');
                        header('Location: ' . URL_ROOT . '/stock');
                        exit();
                    }
                } catch (Exception $e) {
                    // Captura erros específicos do modelo
                    $data['general_err'] = $e->getMessage();
                    $productModel = $this->model('Product');
                    $data['products'] = $productModel->getAllProducts();
                    $data['title'] = 'Entrada de Estoque';
                    $this->view('stock/add', $data);
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
                'observacao' => '',
                'preco_venda' => '',
                'product_id_err' => '',
                'qtd_err' => '',
                'custo_err' => '',
                'preco_venda_err' => '',
                'general_err' => ''
            ];
            $this->view('stock/add', $data);
        }
    }

    public function checkAvailability(){
        // API endpoint para verificar disponibilidade de estoque
        header('Content-Type: application/json');
        
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            echo json_encode(['error' => 'Método não permitido']);
            exit();
        }
        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if(!isset($data['items']) || !is_array($data['items'])){
            echo json_encode(['error' => 'Items não fornecidos']);
            exit();
        }
        
        try {
            $availability = $this->stockModel->checkMultipleProductsAvailability($data['items']);
            echo json_encode(['success' => true, 'availability' => $availability]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }

    // POST: /stock/delete/{id}
    public function delete($id){
        if(!core\Session::isLoggedIn()){
            header('Location: ' . URL_ROOT . '/users/login');
            exit();
        }
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: ' . URL_ROOT . '/stock');
            exit();
        }
        $ok = $this->stockModel->deleteStockItem((int)$id);
        core\Session::flash('stock_message', $ok ? 'Item excluído com sucesso.' : 'Não foi possível excluir. Apenas itens em estoque podem ser excluídos.');
        header('Location: ' . URL_ROOT . '/stock');
        exit();
    }

    // POST: /stock/updateCost/{id}
    public function updateCost($id){
        if(!core\Session::isLoggedIn()){
            header('Location: ' . URL_ROOT . '/users/login');
            exit();
        }
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: ' . URL_ROOT . '/stock');
            exit();
        }
        $newCost = isset($_POST['aquisicao_custo']) ? str_replace([',','.'], ['','.'], $_POST['aquisicao_custo']) : null; // trata vírgulas
        // Melhor: aceitar formato BR e EN
        $raw = $_POST['aquisicao_custo'] ?? '0';
        $normalized = preg_replace('/[^0-9,\.]/','', $raw);
        // converte pt-BR (1.234,56) para 1234.56
        if(strpos($normalized, ',') !== false && strpos($normalized, '.') !== false){
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        } else if (strpos($normalized, ',') !== false){
            $normalized = str_replace(',', '.', $normalized);
        }
        try{
            $ok = $this->stockModel->updateStockItemCost((int)$id, (float)$normalized);
            core\Session::flash('stock_message', $ok ? 'Custo atualizado com sucesso.' : 'Não foi possível atualizar o custo para este item.');
        } catch (Exception $e){
            core\Session::flash('stock_message', 'Erro: ' . $e->getMessage());
        }
        header('Location: ' . URL_ROOT . '/stock');
        exit();
    }
}
