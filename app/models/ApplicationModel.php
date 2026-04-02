<?php
declare(strict_types=1);
require_once ROOT . '/app/config/Database.php';

final class ApplicationModel {
    public function create(array $data): int {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO candidature (id_offre, id_etudiant, lettre_motivation, cv_path, date_candidature)
            VALUES (:id_offre, :id_etudiant, :lm, :cv, NOW())
        ");
        $stmt->execute([
            ':id_offre'    => $data['id_offre'],
            ':id_etudiant' => $data['id_etudiant'],
            ':lm'          => $data['lettre_motivation'],
            ':cv'          => $data['cv_path'],
        ]);
        return (int)$pdo->lastInsertId();
    }
    public function alreadyApplied(int $idOffre, int $idEtudiant): bool {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM candidature
            WHERE id_offre = :o AND id_etudiant = :e
        ");
        $stmt->execute([':o' => $idOffre, ':e' => $idEtudiant]);
        return (int)$stmt->fetchColumn() > 0;
    }
    public function getByStudent(int $idEtudiant): array {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("
            SELECT
                c.id,
                c.lettre_motivation,
                c.cv_path,
                c.date_candidature,
                o.titre        AS offre_titre,
                o.date_fin     AS offre_date_fin,
                e.nom          AS entreprise_nom
            FROM candidature c
            JOIN offre o      ON o.id = c.id_offre
            JOIN entreprise e ON e.id = o.id_entreprise
            WHERE c.id_etudiant = :id
            ORDER BY c.date_candidature DESC
        ");
        $stmt->execute([':id' => $idEtudiant]);
        return $stmt->fetchAll();
    }
    public function getByPilot(int $idPilote): array {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("
            SELECT
                c.id,
                c.lettre_motivation,
                c.cv_path,
                c.date_candidature,
                o.titre        AS offre_titre,
                o.date_fin     AS offre_date_fin,
                e.nom          AS entreprise_nom,
                u.prenom       AS student_prenom,
                u.nom          AS student_nom,
                u.email        AS student_email
            FROM candidature c
            JOIN offre o      ON o.id = c.id_offre
            JOIN entreprise e ON e.id = o.id_entreprise
            JOIN utilisateur u ON u.id = c.id_etudiant
            WHERE u.id_pilote = :id
            ORDER BY c.date_candidature DESC
        ");
        $stmt->execute([':id' => $idPilote]);
        return $stmt->fetchAll();
    }
    public function getById(int $id): array|false {
        $pdo  = Database::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM candidature WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}