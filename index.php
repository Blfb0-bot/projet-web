<?php
define('ROOT', __DIR__);

$controller = $_GET['controller'] ?? 'accueil';
$action     = $_GET['action']     ?? 'index';

$controller = preg_replace('/[^a-zA-Z0-9]/', '', $controller);
$action     = preg_replace('/[^a-zA-Z0-9]/', '', $action);

$class = ucfirst($controller) . 'Controller';
$file  = 'app/controllers/' . $class . '.php';

if (file_exists($file)) {
    require_once $file;
    $ctrl = new $class();
    if (method_exists($ctrl, $action)) {
        $ctrl->$action();
    } else {
        http_response_code(404);
        echo "Action introuvable.";
    }
} else {
    http_response_code(404);
    echo "Controller introuvable : $class";
}
?>