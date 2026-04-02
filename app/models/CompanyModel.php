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
    public function searchByName(string $searchTerm): array {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("
            SELECT id, nom, description, email, telephone, created_at
            FROM entreprise
            WHERE nom LIKE :search
        ");
        $stmt->execute([':search' => "%$searchTerm%"]);
        return $stmt->fetchAll();
    }
    public function evaluer(array $data): void{
        $pdo = Database::getPdo();
        $sql = "
            INSERT INTO evaluation (id_entreprise, id_etudiant, note, commentaire, created_at)
            VALUES (:id_entreprise, :id_etudiant, :note, :commentaire, NOW())
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_entreprise' => $data['id_entreprise'],
            ':id_etudiant' => $data['id_etudiant'],
            ':note' => $data['note'],
            ':commentaire' => $data['commentaire']
        ]);
    }

}