<?php
declare(strict_types=1);

final class Database{
    private static ?PDO $instance = null;
    public static function getPdo(): PDO {
        if (self::$instance === null) {
            // FORCE 127.0.0.1 au lieu de localhost pour WSL
            $host = self::env('DB_HOST', '127.0.0.1');
            $db   = self::env('DB_NAME', 'projet_web');
            $user = self::env('DB_USER', 'root');
            $pass = self::env('DB_PASS', 'Beuvry/0710');
            // Ajout du port par défaut au cas où
            $dsn = "mysql:host=$host;port=3306;dbname=$db;charset=utf8mb4";
            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (\PDOException $e) {
                // STx 11 : On ne print JAMAIS $e->getMessage() directement à l'écran en prod
                // car cela peut révéler ton mot de passe ou ton nom de serveur.
                error_log('DB Error: ' . $e->getMessage()); 
                die('Erreur de connexion : Vérifiez vos identifiants.');
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

