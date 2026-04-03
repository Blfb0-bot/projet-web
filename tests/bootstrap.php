<?php
declare(strict_types=1);

// Point d'entrée PHPUnit.
// On définit ROOT pour que les fichiers du projet puissent faire require_once ROOT . '/...'.

if (!defined('ROOT')) {
    define('ROOT', realpath(__DIR__ . '/..'));
}

// Charger les variables DB depuis .env.example (si pas déjà définies).
$envExamplePath = ROOT . '/.env.example';
if (is_file($envExamplePath) && is_readable($envExamplePath)) {
    $lines = file($envExamplePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }
            $key = trim($parts[0]);
            $val = trim($parts[1]);
            if ($key === '') {
                continue;
            }

            // Si env déjà défini par l'utilisateur / phpunit.xml, on ne force pas.
            if (getenv($key) === false) {
                putenv($key . '=' . $val);
            }
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $val;
            }
        }
    }
}

// DB_NAME par défaut si non override.
if (getenv('DB_NAME') === false || getenv('DB_NAME') === '') {
    putenv('DB_NAME=projet_web_test');
    $_ENV['DB_NAME'] = 'projet_web_test';
}

require_once ROOT . '/tests/BaseModelTestCase.php';

