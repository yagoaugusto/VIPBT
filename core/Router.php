<?php

namespace core;

class Router {
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct(){
        $url = $this->getUrl();

        // Procura por um controller correspondente em /controllers
        if(isset($url[0])){
            $controllerName = $this->getControllerName($url[0]);
            if(file_exists('../controllers/' . $controllerName . '.php')){
                // Se existir, define como controller atual
                $this->currentController = $controllerName;
                // Remove da URL
                unset($url[0]);
            }
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

    private function getControllerName($urlSegment){
        // Try different controller name formats
        $attempts = [
            ucwords($urlSegment), // Publicorders
            ucwords(str_replace('_', '', $urlSegment)), // Publicorders
            str_replace(' ', '', ucwords(str_replace('_', ' ', $urlSegment))), // Publicorders
            // Handle special cases
            'PublicOrders' // Direct mapping for publicorders
        ];

        // Special mappings for known cases
        $specialMappings = [
            'publicorders' => 'PublicOrders',
            'tradeins' => 'TradeIns'
        ];

        if(isset($specialMappings[strtolower($urlSegment)])){
            return $specialMappings[strtolower($urlSegment)];
        }

        // Try standard ucwords first
        foreach($attempts as $attempt){
            if(file_exists('../controllers/' . $attempt . '.php')){
                return $attempt;
            }
        }

        // Default to ucwords
        return ucwords($urlSegment);
    }
}
