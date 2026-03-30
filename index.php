<?php
define('ROOT', __DIR__);

// Activer l'affichage des erreurs en environnement de développement
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Charger les variables d'environnement depuis .env si présent
require_once ROOT . '/app/config/EnvLoader.php';
\EnvLoader::load(ROOT . '/.env');

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