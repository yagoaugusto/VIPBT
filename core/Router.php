<?php

namespace core;

class Router {
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct(){
        $url = $this->getUrl();

        // Procura por um controller correspondente em /controllers
        if(isset($url[0]) && file_exists('../controllers/' . ucwords($url[0]) . '.php')){
            // Se existir, define como controller atual
            $this->currentController = ucwords($url[0]);
            // Remove da URL
            unset($url[0]);
        }

        // Requere o controller
        require_once '../controllers/' . $this->currentController . '.php';

        // Instancia o controller
        $this->currentController = new $this->currentController;

        // Procura pelo segundo parâmetro da URL (método)
        if(isset($url[1])){
            // Verifica se o método existe no controller
            if(method_exists($this->currentController, $url[1])){
                $this->currentMethod = $url[1];
                // Remove da URL
                unset($url[1]);
            }
        }

        // Pega os parâmetros restantes
        $this->params = $url ? array_values($url) : [];

        // Chama o método do controller com os parâmetros
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl(){
        if(isset($_GET['url'])){
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        // Retorna um array vazio se não houver url, para carregar o controller padrão
        return [];
    }
}
