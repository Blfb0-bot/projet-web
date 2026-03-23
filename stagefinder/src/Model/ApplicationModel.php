<?php
declare(strict_types=1);
namespace App\Model;

use PDO;

class ApplicationModel extends BaseModel
{
    public function apply(int $offerId, int $studentId, string $lm, string $cvPath): void
    {
        $this->pdo->prepare(
            "INSERT INTO candidature (id_offre, id_etudiant, lettre_motivation, cv_path)
             VALUES (:o,:s,:lm,:cv)
             ON DUPLICATE KEY UPDATE lettre_motivation=:lm, cv_path=:cv"
        )->execute([':o'=>$offerId,':s'=>$studentId,':lm'=>$lm,':cv'=>$cvPath]);
    }

    public function hasApplied(int $offerId, int $studentId): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM candidature WHERE id_offre=:o AND id_etudiant=:s");
        $stmt->execute([':o'=>$offerId,':s'=>$studentId]);
        return (bool)$stmt->fetch();
    }

    public function getByStudent(int $studentId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.*, o.titre, e.nom AS entreprise_nom
             FROM candidature c
             JOIN offre o ON o.id=c.id_offre
             JOIN entreprise e ON e.id=o.id_entreprise
             WHERE c.id_etudiant=:s ORDER BY c.date_candidature DESC"
        );
        $stmt->execute([':s'=>$studentId]);
        return $stmt->fetchAll();
    }

    public function getByPilot(int $pilotId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.*, o.titre, e.nom AS entreprise_nom, u.nom AS etudiant_nom, u.prenom AS etudiant_prenom
             FROM candidature c
             JOIN offre o ON o.id=c.id_offre
             JOIN entreprise e ON e.id=o.id_entreprise
             JOIN utilisateur u ON u.id=c.id_etudiant
             WHERE u.id_pilote=:p ORDER BY c.date_candidature DESC"
        );
        $stmt->execute([':p'=>$pilotId]);
        return $stmt->fetchAll();
    }
}

class WishlistModel extends BaseModel
{
    public function toggle(int $offerId, int $studentId): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM wishlist WHERE id_offre=:o AND id_etudiant=:s");
        $stmt->execute([':o'=>$offerId,':s'=>$studentId]);
        if ($stmt->fetch()) {
            $this->pdo->prepare("DELETE FROM wishlist WHERE id_offre=:o AND id_etudiant=:s")
                ->execute([':o'=>$offerId,':s'=>$studentId]);
            return false;
        }
        $this->pdo->prepare("INSERT INTO wishlist (id_offre,id_etudiant) VALUES (:o,:s)")
            ->execute([':o'=>$offerId,':s'=>$studentId]);
        return true;
    }

    public function getByStudent(int $studentId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT o.*, e.nom AS entreprise_nom
             FROM wishlist w JOIN offre o ON o.id=w.id_offre
             JOIN entreprise e ON e.id=o.id_entreprise
             WHERE w.id_etudiant=:s ORDER BY o.titre"
        );
        $stmt->execute([':s'=>$studentId]);
        return $stmt->fetchAll();
    }

    public function isInWishlist(int $offerId, int $studentId): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM wishlist WHERE id_offre=:o AND id_etudiant=:s");
        $stmt->execute([':o'=>$offerId,':s'=>$studentId]);
        return (bool)$stmt->fetch();
    }
}

class CompetenceModel extends BaseModel
{
    public function getAll(): array
    {
        return $this->pdo->query("SELECT * FROM competence ORDER BY libelle")->fetchAll();
    }
}
