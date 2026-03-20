<?php
require_once 'config.php';

$controller = $_GET['controller'] ?? 'accueil';
$action     = $_GET['action']     ?? 'index';

// Sécuriser
$controller = preg_replace('/[^a-zA-Z0-9]/', '', $controller);
$action     = preg_replace('/[^a-zA-Z0-9]/', '', $action);

// Construire le nom de classe
$class = ucfirst($controller) . 'Controller';
$file  = 'app/controllers/' . $class . '.php';

if (file_exists($file)) {
    require_once $file;
    $ctrl = new $class();
    if (method_exists($ctrl, $action)) {
        $ctrl->$action();
    } else {
        http_response_code(404);
        require_once 'app/views/pages/404.php';
    }
} else {
    http_response_code(404);
    require_once 'app/views/pages/404.php';
}
?>