<?php
declare(strict_types=1);

require_once ROOT . '/app/config/Database.php';

final class UserModel
{
    public function getByRole(string $role): array
    {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("
            SELECT id, nom, prenom, email, role, id_pilote, created_at
            FROM utilisateur
            WHERE role = :role
            ORDER BY created_at DESC
        ");
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll();
    }
}

