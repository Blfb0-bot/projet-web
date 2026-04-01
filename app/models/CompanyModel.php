<?php
declare(strict_types=1);
require_once ROOT . '/app/config/Database.php';
final class CompanyModel{
    public function getAll(): array{
        $pdo = Database::getPdo();
        $sql = "
            SELECT id, nom, description, email, telephone, created_at
            FROM entreprise
            ORDER BY created_at DESC
        ";
        return $pdo->query($sql)->fetchAll();
    }

    /** Retourne l’id si une entreprise avec ce nom exact existe, sinon null. */
    public function findIdByNom(string $nom): ?int{
        $nom = trim($nom);
        if ($nom === '') {
            return null;
        }
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare('SELECT id FROM entreprise WHERE nom = :n LIMIT 1');
        $stmt->execute([':n' => $nom]);
        $row = $stmt->fetch();
        return $row !== false ? (int)$row['id'] : null;
    }
    public function create(array $data): int{
        $pdo = Database::getPdo();
        $sql = "
            INSERT INTO entreprise (nom, description, email, telephone, created_at)
            VALUES (:nom, :description, :email, :telephone, NOW())
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $data['nom'],
            ':description' => $data['description'] ?? null,
            ':email' => $data['email'] ?? null,
            ':telephone' => $data['telephone'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }
    public function update(int $id, array $data): void{
        $pdo = Database::getPdo();
        $sql = "
            UPDATE entreprise SET
                nom = :nom,
                description = :description,
                email = :email,
                telephone = :telephone
            WHERE id = :id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':nom' => $data['nom'],
            ':description' => $data['description'] ?? null,
            ':email' => $data['email'] ?? null,
            ':telephone' => $data['telephone'] ?? null,
        ]);
        
    }
    public function delete(int $id): void{
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare('DELETE FROM entreprise WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
    /** Crée une entreprise minimale si le nom exact n’existe pas encore. */
    public function findOrCreateByNom(string $nom): ?int{
        $nom = trim($nom);
        if ($nom === '') {
            return null;
        }
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare('SELECT id FROM entreprise WHERE nom = :n LIMIT 1');
        $stmt->execute([':n' => $nom]);
        $row = $stmt->fetch();
        if ($row !== false) {
            return (int)$row['id'];
        }
        $ins = $pdo->prepare('INSERT INTO entreprise (nom) VALUES (:n)');
        $ins->execute([':n' => $nom]);
        return (int)$pdo->lastInsertId();
    }
}

