<?php

use core\Controller;

class Categories extends Controller {
    private $categoryModel;

    public function __construct(){
        $this->categoryModel = $this->model('Category');
    }

    public function index(){
        $categories = $this->categoryModel->getAllCategories();
        $data = [
            'title' => 'Categorias',
            'categories' => $categories
        ];
        $this->view('categories/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'nome' => trim($_POST['nome']),
                'ativo' => isset($_POST['ativo']) ? 1 : 0,
                'nome_err' => ''
            ];

            if(empty($data['nome'])){
                $data['nome_err'] = 'Por favor, insira o nome da categoria.';
            }

            if(empty($data['nome_err'])){
                if($this->categoryModel->addCategory($data)){
                    header('Location: ' . URL_ROOT . '/categories');
                } else {
                    die('Algo deu errado.');
                }
            } else {
                $this->view('categories/add', $data);
            }

        } else {
            $data = [
                'title' => 'Adicionar Categoria',
                'nome' => '',
                'ativo' => 1
            ];
            $this->view('categories/add', $data);
        }
    }

    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'nome' => trim($_POST['nome']),
                'ativo' => isset($_POST['ativo']) ? 1 : 0,
                'nome_err' => ''
            ];

            if(empty($data['nome'])){
                $data['nome_err'] = 'Por favor, insira o nome da categoria.';
            }

            if(empty($data['nome_err'])){
                if($this->categoryModel->updateCategory($data)){
                    header('Location: ' . URL_ROOT . '/categories');
                } else {
                    die('Algo deu errado.');
                }
            } else {
                $this->view('categories/edit', $data);
            }

        } else {
            $category = $this->categoryModel->getCategoryById($id);

            $data = [
                'title' => 'Editar Categoria',
                'id' => $id,
                'nome' => $category->nome,
                'ativo' => $category->ativo
            ];
            $this->view('categories/edit', $data);
        }
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->categoryModel->deleteCategory($id)){
                header('Location: ' . URL_ROOT . '/categories');
            } else {
                die('Algo deu errado.');
            }
        } else {
            header('Location: ' . URL_ROOT . '/categories');
        }
    }
}
