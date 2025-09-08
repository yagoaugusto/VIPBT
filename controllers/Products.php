<?php

use core\Controller;

class Products extends Controller {
    private $productModel;
    private $brandModel;
    private $categoryModel;

    public function __construct(){
        $this->productModel = $this->model('Product');
        $this->brandModel = $this->model('Brand');
        $this->categoryModel = $this->model('Category');
    }

    public function index(){
        $products = $this->productModel->getAllProducts();
        $data = [
            'title' => 'Produtos',
            'products' => $products
        ];
        $this->view('products/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'nome' => trim($_POST['nome']),
                'sku' => trim($_POST['sku']),
                'brand_id' => $_POST['brand_id'],
                'category_id' => $_POST['category_id'],
                'tipo_condicao' => $_POST['tipo_condicao'],
                'ativo' => isset($_POST['ativo']) ? 1 : 0,
                'nome_err' => '',
                'brand_id_err' => '',
                'category_id_err' => ''
            ];

            // Validação
            if(empty($data['nome'])){
                $data['nome_err'] = 'Por favor, insira o nome do produto.';
            }
            if(empty($data['brand_id'])){
                $data['brand_id_err'] = 'Por favor, selecione a marca.';
            }
            if(empty($data['category_id'])){
                $data['category_id_err'] = 'Por favor, selecione a categoria.';
            }
            if(empty($data['nome_err']) && empty($data['brand_id_err']) && empty($data['category_id_err'])){
                if($this->productModel->addProduct($data)){
                    header('Location: ' . URL_ROOT . '/products');
                } else {
                    die('Algo deu errado.');
                }
            } else {
                // Carrega a view com os erros
                $data['brands'] = $this->brandModel->getAllBrands();
                $data['categories'] = $this->categoryModel->getAllCategories();
                $this->view('products/add', $data);
            }

        } else {
            $data = [
                'title' => 'Adicionar Produto',
                'nome' => '',
                'sku' => '',
                'brand_id' => '',
                'category_id' => '',
                'tipo_condicao' => 'novo',
                'ativo' => 1,
                'brands' => $this->brandModel->getAllBrands(),
                'categories' => $this->categoryModel->getAllCategories()
            ];
            $this->view('products/add', $data);
        }
    }

    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'nome' => trim($_POST['nome']),
                'sku' => trim($_POST['sku']),
                'brand_id' => $_POST['brand_id'],
                'category_id' => $_POST['category_id'],
                'tipo_condicao' => $_POST['tipo_condicao'],
                'ativo' => isset($_POST['ativo']) ? 1 : 0,
                'nome_err' => '',
                'brand_id_err' => '',
                'category_id_err' => ''
            ];

            // Validação
            if(empty($data['nome'])){
                $data['nome_err'] = 'Por favor, insira o nome do produto.';
            }
            if(empty($data['brand_id'])){
                $data['brand_id_err'] = 'Por favor, selecione a marca.';
            }
            if(empty($data['category_id'])){
                $data['category_id_err'] = 'Por favor, selecione a categoria.';
            }
            if(empty($data['nome_err']) && empty($data['brand_id_err']) && empty($data['category_id_err'])){
                if($this->productModel->updateProduct($data)){
                    header('Location: ' . URL_ROOT . '/products');
                } else {
                    die('Algo deu errado.');
                }
            } else {
                // Carrega a view com os erros
                $data['brands'] = $this->brandModel->getAllBrands();
                $data['categories'] = $this->categoryModel->getAllCategories();
                $this->view('products/edit', $data);
            }

        } else {
            $product = $this->productModel->getProductById($id);

            $data = [
                'title' => 'Editar Produto',
                'id' => $id,
                'nome' => $product->nome,
                'sku' => $product->sku,
                'brand_id' => $product->brand_id,
                'category_id' => $product->category_id,
                'tipo_condicao' => $product->tipo_condicao,
                'ativo' => $product->ativo,
                'brands' => $this->brandModel->getAllBrands(),
                'categories' => $this->categoryModel->getAllCategories()
            ];
            $this->view('products/edit', $data);
        }
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->productModel->deleteProduct($id)){
                header('Location: ' . URL_ROOT . '/products');
            } else {
                die('Algo deu errado.');
            }
        } else {
            header('Location: ' . URL_ROOT . '/products');
        }
    }
}
