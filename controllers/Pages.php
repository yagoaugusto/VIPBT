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
            'description' => 'A estrutura base do seu sistema está funcionando.'
        ];

        // Carrega a view da página inicial passando os dados
        $this->view('pages/index', $data);
    }

    public function about(){
        $data = [
            'title' => 'Sobre Nós',
            'description' => 'Página sobre a loja.'
        ];

        $this->view('pages/about', $data);
    }
}