<?php

use core\Controller;
use core\Session;

class Users extends Controller {
    private $userModel;

    public function __construct(){
        $this->userModel = $this->model('UserModel');
    }

    public function index(){
        // Acessível apenas para admin
        if(!Session::isLoggedIn() || Session::get('user_perfil') != 'admin'){
            header('Location: ' . URL_ROOT . '/users/login');
            exit();
        }

        $users = $this->userModel->getAllUsers();
        $data = [
            'title' => 'Usuários do Sistema',
            'users' => $users
        ];
        $this->view('users/index', $data);
    }

    public function add(){
        if(!Session::isLoggedIn() || Session::get('user_perfil') != 'admin'){
            header('Location: ' . URL_ROOT . '/users/login');
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'nome' => trim($_POST['nome']),
                'email' => trim($_POST['email']),
                'senha' => trim($_POST['senha']),
                'confirma_senha' => trim($_POST['confirma_senha']),
                'perfil' => $_POST['perfil'],
                'comissao' => $_POST['comissao'] ?? 0,
                'nome_err' => '',
                'email_err' => '',
                'senha_err' => '',
                'confirma_senha_err' => ''
            ];

            // Validação
            if(empty($data['nome'])){
                $data['nome_err'] = 'Por favor, insira o nome.';
            }
            if(empty($data['email'])){
                $data['email_err'] = 'Por favor, insira o e-mail.';
            } elseif($this->userModel->findUserByEmail($data['email'])){
                $data['email_err'] = 'E-mail já cadastrado.';
            }
            if(empty($data['senha'])){
                $data['senha_err'] = 'Por favor, insira a senha.';
            } elseif(strlen($data['senha']) < 6){
                $data['senha_err'] = 'A senha deve ter no mínimo 6 caracteres.';
            }
            if(empty($data['confirma_senha'])){
                $data['confirma_senha_err'] = 'Por favor, confirme a senha.';
            } else {
                if($data['senha'] != $data['confirma_senha']){
                    $data['confirma_senha_err'] = 'As senhas não coincidem.';
                }
            }

            if(empty($data['nome_err']) && empty($data['email_err']) && empty($data['senha_err']) && empty($data['confirma_senha_err'])){
                // Hash da senha
                $data['senha_hash'] = password_hash($data['senha'], PASSWORD_DEFAULT);

                if($this->userModel->register($data)){
                    Session::flash('user_message', 'Usuário cadastrado com sucesso!');
                    header('Location: ' . URL_ROOT . '/users');
                } else {
                    die('Algo deu errado.');
                }
            } else {
                $this->view('users/add', $data);
            }

        } else {
            $data = [
                'title' => 'Adicionar Usuário',
                'nome' => '',
                'email' => '',
                'senha' => '',
                'confirma_senha' => '',
                'perfil' => 'vendedor',
                'comissao' => 5.00
            ];
            $this->view('users/add', $data);
        }
    }
    
    public function login(){
        if(Session::isLoggedIn()){
            header('Location: ' . URL_ROOT);
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data = [
                'email' => trim($_POST['email']),
                'senha' => trim($_POST['senha']),
                'email_err' => '',
                'senha_err' => '',
                'title' => 'Login' // Adicionado aqui
            ];

            if(empty($data['email'])){
                $data['email_err'] = 'Por favor, insira o e-mail.';
            }
            if(empty($data['senha'])){
                $data['senha_err'] = 'Por favor, insira a senha.';
            }

            // Verifica o email
            if($this->userModel->findUserByEmail($data['email'])){
                // Usuário encontrado
            } else {
                $data['email_err'] = 'Nenhum usuário encontrado com este e-mail.';
            }

            if(empty($data['email_err']) && empty($data['senha_err'])){
                $loggedInUser = $this->userModel->login($data['email'], $data['senha']);

                if($loggedInUser){
                    // Cria a sessão
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['senha_err'] = 'Senha incorreta.';
                    $this->view('users/login', $data);
                }
            } else {
                $this->view('users/login', $data);
            }

        } else {
            $data = [
                'title' => 'Login',
                'email' => '',
                'senha' => '',
                'email_err' => '',
                'senha_err' => '',
            ];
            $this->view('users/login', $data);
        }
    }

    public function createUserSession($user){
        Session::set('user_id', $user->id);
        Session::set('user_email', $user->email);
        Session::set('user_name', $user->nome);
        Session::set('user_perfil', $user->perfil);
        header('Location: ' . URL_ROOT);
    }

    public function logout(){
        Session::remove('user_id');
        Session::remove('user_email');
        Session::remove('user_name');
        Session::remove('user_perfil');
        Session::destroy();
        header('Location: ' . URL_ROOT . '/users/login');
    }
}
