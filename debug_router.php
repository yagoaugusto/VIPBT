<?php
// Debug router - run from public directory

chdir('public');
require_once '../config/config.php';

// Autoload for loading classes automatically
spl_autoload_register(function ($className) {
    // Convert namespace to file path
    // E.g: core\Router becomes core/Router.php
    $file = '../' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        // Try loading as a controller
        $controllerFile = '../controllers/' . $className . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        }
    }
});

echo "Testing router functionality:\n";

// Simulate the URL
$_GET['url'] = 'publicorders/consulta';

echo "URL: " . $_GET['url'] . "\n";

$router = new core\Router();
echo "Router should have loaded PublicOrders controller\n";
?>