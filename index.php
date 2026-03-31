<?php
session_start();
define('ROOT', __DIR__);

// 3) (Optionnel) paramètres de cookie de session sûrs — décommentez/adaptez si vous avez HTTPS
// session_set_cookie_params([ 'lifetime' => 0, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Lax' ]);

// CSRF token global, protège les formulaires contre les requêtes forcées
if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Utiliser random_bytes si disponible (sécurisé)
    } catch (Exception $e) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32)); // Fallback (moins probable)
    }
} else {
    // Le token CSRF existe déjà dans la session — rien à faire
}
// Table de routes minimale
$routes = [
    'accueil'   => ['AccueilController','index'],
    'companies' => ['CompaniesController','index'],
    'offers'    => ['OffersController','index'],
    'Pilots'    => ['PilotsController','index'],
    'Students'  => ['StudentsController','index'],
];
// Lecture des paramètres de la requête
$controller = $_GET['controller'] ?? 'accueil';
$action     = $_GET['action'] ?? 'index';
// Validation basique des noms fournis
if (!preg_match('/^[a-zA-Z0-9_]+$/', $controller)) {
    http_response_code(400);
    echo 'Nom de contrôleur invalide';
    exit;
} else {
    // nom de contrôleur valide
}
// Vérifier que le contrôleur est connu dans la table de routes
if (!isset($routes[$controller])) {
    http_response_code(404);
    echo 'Page non trouvée';
    exit;
} else {
    [$class, $method] = $routes[$controller]; // Récupération de la classe et de la méthode attendues
}
//Construire le chemin du fichier contrôleur et vérifier son existence
$ctrlFile = ROOT . '/app/controllers/' . $class . '.php';
if (!file_exists($ctrlFile)) {
    http_response_code(500);
    echo 'Contrôleur introuvable';
    exit;
} else {
    require_once $ctrlFile; // Charger le fichier de contrôleur
}
//Vérifier que la classe du contrôleur est définie
if (!class_exists($class)) {
    http_response_code(500);
    echo 'Classe de contrôleur introuvable';
    exit;
} else {
    $ctrl = new $class(); // Instancier le contrôleur
}
//Vérifier que la méthode demandée existe sur l'instance
if (!method_exists($ctrl, $method)) {
    http_response_code(500);
    echo 'Action introuvable';
    exit;
} else {
    $ctrl->$method(); // Exécuter l'action du contrôleur en appelant la méthode d'action
}