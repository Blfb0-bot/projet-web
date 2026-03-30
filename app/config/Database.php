<?php
declare(strict_types=1);

use PDO;

final class Database
{
    private static ?PDO $instance = null;

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

    public static function getPdo(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Charge .env de manière optionnelle
        $envFile = __DIR__ . '/../../.env';
        require_once __DIR__ . '/EnvLoader.php';
        EnvLoader::load($envFile);

        $host = self::env('DB_HOST', 'localhost');
        $db   = self::env('DB_NAME', 'stagefinder');
        $user = self::env('DB_USER', 'sfuser');
        $pass = self::env('DB_PASS', '');

        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        self::$instance = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$instance;
    }
}

