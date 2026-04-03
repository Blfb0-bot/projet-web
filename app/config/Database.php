<?php
declare(strict_types=1);

final class Database{
    private static ?PDO $instance = null;
    public static function getPdo(): PDO {
        if (self::$instance === null) {
            // Permet d'utiliser une DB dédiée en tests (via vars d'environnement).
            // Par défaut, on garde les valeurs historiques du projet.
            $host = (string)(self::env('DB_HOST', '127.0.0.1'));
            $db   = (string)(self::env('DB_NAME', 'projet_web'));
            $user = (string)(self::env('DB_USER', 'root'));
            $pass = (string)(self::env('DB_PASS', 'Beuvry/0710'));
            $port = (string)(self::env('DB_PORT', '3306'));

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            
            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (\PDOException $e) {
                // Affiche l'erreur complète juste pour le debug
                die("Échec du lien : " . $e->getMessage());
            }
        }
        return self::$instance;
    }
    private static function env(string $key, ?string $default = null): ?string{
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string)$_ENV[$key];
        }
        return $default;
    }
}

