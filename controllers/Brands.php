<?php

use core\Controller;

class Brands extends Controller {
    private $brandModel;

    public function __construct(){
        $this->brandModel = $this->model('Brand');
    }

    // Método principal, lista todas as marcas
    public function index(){
        $brands = $this->brandModel->getAllBrands();
        $data = [
            'title' => 'Marcas',
            'brands' => $brands
        ];
        $this->view('brands/index', $data);
    }

    // Exibe o formulário de adição e trata o POST
    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Limpa os dados do POST
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'nome' => trim($_POST['nome']),
                'ativo' => isset($_POST['ativo']) ? 1 : 0,
                'nome_err' => ''
            ];

            // Validação
            if(empty($data['nome'])){
                $data['nome_err'] = 'Por favor, insira o nome da marca.';
            }

            // Se não houver erros, adiciona a marca
            if(empty($data['nome_err'])){
                if($this->brandModel->addBrand($data)){
                    // Redireciona para a lista de marcas
                    header('Location: ' . URL_ROOT . '/brands');
                } else {
                    die('Algo deu errado.');
                }
            } else {
                // Carrega a view com os erros
                $this->view('brands/add', $data);
            }

        } else {
            // Carrega o formulário
            $data = [
                'title' => 'Adicionar Marca',
                'nome' => '',
                'ativo' => 1
            ];
            $this->view('brands/add', $data);
        }
    }

    // Exibe o formulário de edição e trata o POST
    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Limpa os dados do POST
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'nome' => trim($_POST['nome']),
                'ativo' => isset($_POST['ativo']) ? 1 : 0,
                'nome_err' => ''
            ];

            // Validação
            if(empty($data['nome'])){
                $data['nome_err'] = 'Por favor, insira o nome da marca.';
            }

            // Se não houver erros, atualiza a marca
            if(empty($data['nome_err'])){
                if($this->brandModel->updateBrand($data)){
                    // Redireciona para a lista de marcas
                    header('Location: ' . URL_ROOT . '/brands');
                } else {
                    die('Algo deu errado.');
                }
            } else {
                // Carrega a view com os erros
                $this->view('brands/edit', $data);
            }

        } else {
            // Busca a marca no banco
            $brand = $this->brandModel->getBrandById($id);

            // Carrega o formulário
            $data = [
                'title' => 'Editar Marca',
                'id' => $id,
                'nome' => $brand->nome,
                'ativo' => $brand->ativo
            ];
            $this->view('brands/edit', $data);
        }
    }

    // Deleta uma marca
    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->brandModel->deleteBrand($id)){
                // Redireciona para a lista de marcas
                header('Location: ' . URL_ROOT . '/brands');
            } else {
                die('Algo deu errado.');
            }
        } else {
            // Redireciona se não for POST
            header('Location: ' . URL_ROOT . '/brands');
        }
    }
}
