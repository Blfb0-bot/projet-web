<?php
declare(strict_types=1);
require_once ROOT . '/app/config/Database.php';

class WishlistModel {
    private PDO $pdo;
    public function __construct() {
        $this->pdo = Database::getPdo();
    }
    // SF23 — Récupérer toutes les offres de la wish-list de l'étudiant
    public function getByEtudiant(int $id_etudiant): array {
        $stmt = $this->pdo->prepare("
            SELECT o.* FROM offre o
            JOIN wishlist w ON w.id_offre = o.id
            WHERE w.id_etudiant = :id_etudiant
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([':id_etudiant' => $id_etudiant]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // SF24 — Ajouter une offre à la wish-list
    public function ajouter(int $id_etudiant, int $id_offre): bool {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO wishlist (id_etudiant, id_offre)
                VALUES (:id_etudiant, :id_offre)
            ");
            return $stmt->execute([
                ':id_etudiant' => $id_etudiant,
                ':id_offre'    => $id_offre,
            ]);
        } catch (PDOException $e) {
            // PRIMARY KEY composite empêche les doublons
            return false;
        }
    }
    // SF25 — Retirer une offre de la wish-list
    public function retirer(int $id_etudiant, int $id_offre): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM wishlist
            WHERE id_etudiant = :id_etudiant AND id_offre = :id_offre
        ");
        return $stmt->execute([
            ':id_etudiant' => $id_etudiant,
            ':id_offre'    => $id_offre,
        ]);
    }
    // Utilitaire — vérifier si une offre est déjà dans la wish-list
    public function existe(int $id_etudiant, int $id_offre): bool {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM wishlist
            WHERE id_etudiant = :id_etudiant AND id_offre = :id_offre
        ");
        $stmt->execute([
            ':id_etudiant' => $id_etudiant,
            ':id_offre'    => $id_offre,
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }
}