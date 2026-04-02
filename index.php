<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL); // Affiche les erreurs pour le développement

ini_set('session.cookie_httponly', '1'); // Empêche le vol de session par JS
ini_set('session.use_only_cookies', '1'); // Interdit de passer l'ID de session dans l'URL
ini_set('session.cookie_samesite', 'Strict'); // Protection contre le CSRF

// Active le flag Secure uniquement si HTTPS est détecté
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', '1');
}
session_start();
define('ROOT', __DIR__);

if (!isset($_SESSION['user_role'])) {
    $_SESSION['user_role'] = 'anonyme';
}
// --- Sécurité CSRF ---
if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

// --- Table de routes ---
$routes = [
    'accueil'   => 'AccueilController',
    'companies' => 'CompaniesController',
    'offers'    => 'OffersController',
    'pilots'    => 'PilotsController',
    'students'  => 'StudentsController',
    'auth'      => 'AuthController',
    'user'     => 'UserController',
];

// --- Lecture des paramètres ---
$controllerKey = $_GET['controller'] ?? 'accueil';
$method        = $_GET['action'] ?? 'index'; // On récupère l'action (create, update, delete...)

// --- Validation du nom du contrôleur ---
if (!preg_match('/^[a-zA-Z0-9_]+$/', $controllerKey)) {
    http_response_code(400);
    echo 'Nom de contrôleur invalide';
    exit;
}
// --- Vérification de l'existence dans les routes ---
if (!isset($routes[$controllerKey])) {
    http_response_code(404);
    echo 'Page non trouvée';
    exit;
}

$class = $routes[$controllerKey];

// --- Chargement du fichier contrôleur ---
$ctrlFile = ROOT . '/app/controllers/' . $class . '.php';

if (!file_exists($ctrlFile)) {
    http_response_code(500);
    echo 'Fichier contrôleur introuvable : ' . htmlspecialchars($class);
    exit;
}

require_once $ctrlFile;

// --- Instanciation et exécution ---
if (class_exists($class)) {
    $ctrl = new $class();
    
    // Vérification dynamique : est-ce que la méthode (index, create, update...) existe ?
    if (method_exists($ctrl, $method)) {
        $ctrl->$method(); 
    } else {
        http_response_code(404);
        echo "L'action '" . htmlspecialchars($method) . "' n'existe pas dans le contrôleur " . htmlspecialchars($class);
    }
} else {
    http_response_code(500);
    echo "La classe '" . htmlspecialchars($class) . "' est introuvable dans son fichier.";
}