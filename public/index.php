<?php
// Carrega o arquivo de configuração
require_once '../config/config.php';
// Carrega o helper de sessão
require_once '../core/Session.php';

// Autoload para carregar as classes automaticamente
spl_autoload_register(function ($className) {
    // Converte o namespace em caminho de arquivo
    // Ex: core\Router se torna core/Router.php
    $file = '../' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        // Tenta carregar como um controller
        $controllerFile = '../controllers/' . $className . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        }
    }
});


// Inicia o roteador
$router = new core\Router();
