<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';

final class CompanyModel
{
    public function getAll(): array
    {
        $pdo = Database::getPdo();
        $sql = "
            SELECT id, nom, description, email, telephone, created_at
            FROM entreprise
            ORDER BY created_at DESC
        ";
        return $pdo->query($sql)->fetchAll();
    }
}

