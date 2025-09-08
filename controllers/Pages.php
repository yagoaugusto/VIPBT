<?php

use core\Controller;

class Pages extends Controller {
    public function __construct(){
        // Construtor pode ser usado para carregar models no futuro
    }

    public function index(){
        // Dados a serem passados para a view
        $data = [
            'title' => 'Bem-vindo à VIP LOJA BT',
            'description' => 'Sua plataforma completa para gestão de Beach Tennis. Acesse rapidamente todas as funcionalidades do sistema.'
        ];

        // Carrega a view da página inicial passando os dados
        $this->view('pages/index', $data);
    }

    
}