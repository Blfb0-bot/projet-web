<?php
declare(strict_types=1);

require_once ROOT . '/app/config/Database.php';

final class OfferModel
{
    public function getAll(): array
    {
        $pdo = Database::getPdo();
        $sql = "
            SELECT
                o.id,
                o.titre,
                o.description,
                o.remuneration,
                o.date_debut,
                o.date_fin,
                o.duree_mois,
                o.created_at,
                e.nom AS entreprise_nom,
                (SELECT COUNT(*) FROM candidature ca WHERE ca.id_offre = o.id) AS nb_candidatures,
                GROUP_CONCAT(DISTINCT c.libelle ORDER BY c.libelle SEPARATOR ', ') AS competences
            FROM offre o
            JOIN entreprise e ON o.id_entreprise = e.id
            LEFT JOIN offre_competence oc ON oc.id_offre = o.id
            LEFT JOIN competence c ON c.id = oc.id_competence
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ";

        return $pdo->query($sql)->fetchAll();
    }
}

