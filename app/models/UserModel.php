<?php
declare(strict_types=1);
require_once ROOT . '/app/config/Database.php';
final class UserModel{
    public function getByRole(string $role): array{
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
    public function getByEmail(string $email): ?array {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
    public function findIdByPrenomAndNom(string $prenom, string $nom): ?int{
        $prenom = trim($prenom);
        $nom = trim($nom);
        if ($prenom === '' || $nom === '') {
            return null;
        }
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare('SELECT id FROM utilisateur WHERE prenom = :p AND nom = :n LIMIT 1');
        $stmt->execute([':p' => $prenom, ':n' => $nom]);
        $row = $stmt->fetch();
        return $row !== false ? (int)$row['id'] : null;
    }
    public function create(array $data): int {
        $pdo = Database::getPdo();
        $sql = "
            INSERT INTO utilisateur (prenom, nom, email, role, mot_de_passe, id_pilote, created_at)
            VALUES (:prenom, :nom, :email, :role, :mdp, :id_pilote, NOW())
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':prenom'    => $data['prenom'],
            ':nom'       => $data['nom'],
            ':email'     => $data['email'],
            ':role'      => $data['role'] ?? 'etudiant', // Rôle par défaut
            ':mdp'       => $data['mot_de_passe'],       // Le mot de passe haché
            ':id_pilote' => $data['id_pilote'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }
    public function update(int $id, array $data): void{
        $pdo = Database::getPdo();
        $sql = "
            UPDATE utilisateur SET
                prenom = :prenom,
                nom = :nom,
                email = :email,
                role = :role,
                id_pilote = :id_pilote
            WHERE id = :id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':prenom' => $data['prenom'],
            ':nom' => $data['nom'],
            ':email' => $data['email'] ?? null,
            ':role' => $data['role'] ?? null,
            ':id_pilote' => $data['id_pilote'] ?? null,
        ]);
        
    }
    public function delete(int $id): void{
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare('DELETE FROM utilisateur WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}

