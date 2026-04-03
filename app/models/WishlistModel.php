<?php
declare(strict_types=1);
require_once ROOT . '/app/config/Database.php';

class WishlistModel {
    private PDO $pdo;
    public function __construct() {
        $this->pdo = Database::getPdo();
    }
    // SF23 — Récupérer toutes les offres de la wish-list de l'étudiant
    public function getByEtudiant(int $etudiant_id): array {
        $stmt = $this->pdo->prepare("
            SELECT o.* FROM offres o
            JOIN wishlist w ON w.offre_id = o.id
            WHERE w.etudiant_id = :etudiant_id
            ORDER BY w.date_ajout DESC
        ");
        $stmt->execute([':etudiant_id' => $etudiant_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // SF24 — Ajouter une offre à la wish-list
    public function ajouter(int $etudiant_id, int $offre_id): bool {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO wishlist (etudiant_id, offre_id)
                VALUES (:etudiant_id, :offre_id)
            ");
            return $stmt->execute([
                ':etudiant_id' => $etudiant_id,
                ':offre_id'    => $offre_id,
            ]);
        } catch (PDOException $e) {
            return false; // doublon bloqué par UNIQUE KEY
        }
    }
    // SF25 — Retirer une offre de la wish-list
    public function retirer(int $etudiant_id, int $offre_id): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM wishlist
            WHERE etudiant_id = :etudiant_id AND offre_id = :offre_id
        ");
        return $stmt->execute([
            ':etudiant_id' => $etudiant_id,
            ':offre_id'    => $offre_id,
        ]);
    }
    // Utilitaire — vérifier si une offre est déjà dans la wish-list
    public function existe(int $etudiant_id, int $offre_id): bool {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM wishlist
            WHERE etudiant_id = :etudiant_id AND offre_id = :offre_id
        ");
        $stmt->execute([
            ':etudiant_id' => $etudiant_id,
            ':offre_id'    => $offre_id,
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }
}