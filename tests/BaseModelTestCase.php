<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once ROOT . '/app/config/Database.php';
require_once ROOT . '/app/models/UserModel.php';
require_once ROOT . '/app/models/CompanyModel.php';
require_once ROOT . '/app/models/OfferModel.php';
require_once ROOT . '/app/models/ApplicationModel.php';
require_once ROOT . '/app/models/WishlistModel.php';

abstract class BaseModelTestCase extends TestCase
{
    protected PDO $pdo;

    protected static function env(string $key, string $default): string
    {
        $v = getenv($key);
        if ($v !== false && $v !== '') {
            return $v;
        }
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string)$_ENV[$key];
        }
        return $default;
    }

    public static function setUpBeforeClass(): void
    {
        $host = self::env('DB_HOST', '127.0.0.1');
        $dbName = self::env('DB_NAME', 'projet_web_test');
        $user = self::env('DB_USER', 'root');
        $pass = self::env('DB_PASS', 'Beuvry/0710');
        $port = self::env('DB_PORT', '3306');

        $dsnAdmin = "mysql:host={$host};port={$port};charset=utf8mb4";
        $pdoAdmin = new PDO($dsnAdmin, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Créer la DB de test si nécessaire.
        $pdoAdmin->exec(
            "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );

        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
        ]);

        $schemaPath = ROOT . '/db/schema.sql';
        $schemaSql = is_file($schemaPath) ? (string)file_get_contents($schemaPath) : '';
        if ($schemaSql === '') {
            throw new RuntimeException('Impossible de lire db/schema.sql');
        }

        // Recharger la structure.
        $pdo->exec($schemaSql);
    }

    protected function setUp(): void
    {
        $this->pdo = Database::getPdo();
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        try {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        } catch (Throwable) {
            // On ignore pour éviter de masquer l'échec réel du test.
        }
    }

    protected function createUser(array $overrides): int
    {
        $model = new UserModel();
        $data = array_merge([
            'prenom' => 'Prenom',
            'nom' => 'Nom',
            'email' => 'user_' . uniqid('', true) . '@test.local',
            'mot_de_passe' => 'Password!123',
            'role' => 'visiteur',
            'id_pilote' => null,
        ], $overrides);

        return $model->create($data);
    }

    protected function createCompany(array $overrides): int
    {
        $model = new CompanyModel();
        $data = array_merge([
            'nom' => 'Company_' . uniqid('', true),
            'description' => 'desc',
            'email' => 'contact_' . uniqid('', true) . '@test.local',
            'telephone' => '0000000000',
        ], $overrides);

        return $model->create($data);
    }

    protected function createOffer(array $overrides): int
    {
        $model = new OfferModel();
        $data = array_merge([
            'id_entreprise' => null,
            'titre' => 'Offer_' . uniqid('', true),
            'description' => 'desc',
            'remuneration' => 1000.00,
            'date_debut' => '2026-01-01',
            'date_fin' => '2026-06-01',
        ], $overrides);

        if ($data['id_entreprise'] === null) {
            throw new InvalidArgumentException('createOffer: id_entreprise est requis');
        }

        return $model->create($data);
    }
}

