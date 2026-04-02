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
    public function searchByRoleAndName(string $role, string $term): array {
        $pdo = Database::getPdo();
        $search = '%' . $term . '%';

        $stmt = $pdo->prepare("
            SELECT * FROM utilisateur 
            WHERE role = :role 
            AND (nom LIKE :t1 OR prenom LIKE :t2 OR email LIKE :t3)
            ORDER BY created_at DESC
        ");
        
        $stmt->execute([
            ':role' => $role,
            ':t1'   => $search,
            ':t2'   => $search,
            ':t3'   => $search
        ]);
        
        return $stmt->fetchAll();
    }
    public function create(array $data): int {
        $pdo = Database::getPdo();
        $hashedPassword = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);
        $sql = "
            INSERT INTO utilisateur (prenom, nom, email, role, mot_de_passe, id_pilote, created_at)
            VALUES (:prenom, :nom, :email, :role, :mdp, :id_pilote, NOW())
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':prenom'    => $data['prenom'],
            ':nom'       => $data['nom'],
            ':email'     => $data['email'],
            ':role'      => $data['role'] ?? 'visiteur', // Rôle par défaut
            ':mdp'       => $hashedPassword,       // Le mot de passe haché
            ':id_pilote' => $data['id_pilote'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }
    public function update(int $id, array $data): void{
        $pdo = Database::getPdo();
        $sql = "
            UPDATE utilisateur SET
                prenom = :prenom,
                nom = :nom
            WHERE id = :id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':prenom' => $data['prenom'],
            ':nom' => $data['nom'],
        ]);
        
    }
    public function delete(int $id): void{
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare('DELETE FROM utilisateur WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
    public function verifyPassword($userId, $password){
        $user = $this->getUserById($userId);

        if (!$user) {
            return false;
        }
        return password_verify($password, $user['mot_de_passe']);
    }
    public function getUserById($id){
        $pdo = Database::getPdo();
        $sql = "SELECT * FROM utilisateur WHERE id = :id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    public function updatePassword($userId, $newPassword){
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE utilisateur 
                SET mot_de_passe = :mot_de_passe 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':mot_de_passe', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}