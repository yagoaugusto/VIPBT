<?php

namespace core;

class Controller {
    // Método base para carregar models
    public function model($model){
        // Requere o arquivo do model
        require_once '../models/' . $model . '.php';
        // Instancia o model
        return new $model();
    }

    // Método base para carregar views
    public function view($view, $data = [], $useLayout = true){
        // Constrói o caminho para o arquivo da view
        $viewFile = '../views/' . $view . '.php';

        // Verifica se o arquivo da view existe
        if(file_exists($viewFile)){
            // Extrai os dados para que possam ser usados como variáveis na view
            extract($data);
            
            if($useLayout){
                // O conteúdo da view será capturado e inserido no layout
                ob_start();
                require $viewFile;
                $content = ob_get_clean();

                // Inclui o layout principal
                require_once '../views/layouts/main.php';
            } else {
                // Inclui a view diretamente sem layout
                require $viewFile;
            }
        } else {
            // A view não existe
            die('View não encontrada: ' . $viewFile);
        }
    }
}
