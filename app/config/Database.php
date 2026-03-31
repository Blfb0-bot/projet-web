<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $instance = null;

    public static function getPdo(): PDO{
        if (self::$instance === null) {
            $host = self::env('DB_HOST', 'localhost');
            $db   = self::env('DB_NAME', 'projet_web');
            $user = self::env('DB_USER', 'root');
            $pass = self::env('DB_PASS', 'Beuvry/0710');
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (\PDOException $e) {
                error_log('DB Error: ' . $e->getMessage());
                die('Erreur de connexion à la base de données.');
            }
        }
        return self::$instance;
    }

    private static function env(string $key, ?string $default = null): ?string
    {
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

