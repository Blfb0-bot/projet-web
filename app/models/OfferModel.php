<?php
declare(strict_types=1);
require_once ROOT . '/app/config/Database.php';
final class OfferModel{
    public function getAll(): array{
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
                o.id_entreprise,
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
    public function findIdByTitreAndCompany(string $titre, int $idEntreprise): ?int{
        $titre = trim($titre);
        $idEntreprise = max(0, $idEntreprise);
        if ($titre === '' || $idEntreprise <= 0) {
            return null;
        }
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare('SELECT id FROM offre WHERE titre = :t AND id_entreprise = :e LIMIT 1');
        $stmt->execute([':t' => $titre, ':e' => $idEntreprise]);
        $row = $stmt->fetch();
        return $row !== false ? (int)$row['id'] : null;
    }
    public function create(array $data): int{
        $pdo = Database::getPdo();
        $duree = self::computeDureeMois($data['date_debut'] ?? null, $data['date_fin'] ?? null);
        $sql = "
            INSERT INTO offre (
                id_entreprise, titre, description, remuneration,
                date_debut, date_fin, duree_mois, created_at
            )
            VALUES (
                :id_entreprise, :titre, :description, :remuneration,
                :date_debut, :date_fin, :duree_mois, NOW()
            )
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_entreprise' => $data['id_entreprise'],
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':remuneration' => $data['remuneration'],
            ':date_debut' => $data['date_debut'],
            ':date_fin' => $data['date_fin'],
            ':duree_mois' => $duree,
        ]);
        return (int)$pdo->lastInsertId();
    }
    public function update(int $id, array $data): void{
        $pdo = Database::getPdo();
        $duree = self::computeDureeMois($data['date_debut'] ?? null, $data['date_fin'] ?? null);
        $sql = "
            UPDATE offre SET
                id_entreprise = :id_entreprise,
                titre = :titre,
                description = :description,
                remuneration = :remuneration,
                date_debut = :date_debut,
                date_fin = :date_fin,
                duree_mois = :duree_mois
            WHERE id = :id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':id_entreprise' => $data['id_entreprise'],
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':remuneration' => $data['remuneration'],
            ':date_debut' => $data['date_debut'],
            ':date_fin' => $data['date_fin'],
            ':duree_mois' => $duree,
        ]);
    }
    public function delete(int $id): void{
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare('DELETE FROM offre WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
    public function syncCompetencesForOffer(int $idOffre, string $competencesCsv): void{
        $labels = self::parseCompetenceLabels($competencesCsv);
        $pdo = Database::getPdo();
        $pdo->prepare('DELETE FROM offre_competence WHERE id_offre = :id')->execute([':id' => $idOffre]);
        foreach ($labels as $libelle) {
            $idComp = $this->ensureCompetenceId($pdo, $libelle);
            $ins = $pdo->prepare(
                'INSERT INTO offre_competence (id_offre, id_competence) VALUES (:o, :c)'
            );
            $ins->execute([':o' => $idOffre, ':c' => $idComp]);
        }
    }
    private function ensureCompetenceId(\PDO $pdo, string $libelle): int{
        $sel = $pdo->prepare('SELECT id FROM competence WHERE libelle = :l LIMIT 1');
        $sel->execute([':l' => $libelle]);
        $row = $sel->fetch();
        if ($row !== false) {
            return (int)$row['id'];
        }
        $ins = $pdo->prepare('INSERT INTO competence (libelle) VALUES (:l)');
        $ins->execute([':l' => $libelle]);
        return (int)$pdo->lastInsertId();
    }
    private static function parseCompetenceLabels(string $csv): array{
        $parts = preg_split('/[,;]/', $csv) ?: [];
        $out = [];
        foreach ($parts as $p) {
            $t = trim($p);
            if ($t !== '') {
                $out[] = $t;
            }
        }
        return $out;
    }
    private static function computeDureeMois(?string $dateDebut, ?string $dateFin): ?int{
        if ($dateDebut === null || $dateDebut === '' || $dateFin === null || $dateFin === '') {
            return null;
        }
        try {
            $d1 = new \DateTimeImmutable($dateDebut);
            $d2 = new \DateTimeImmutable($dateFin);
            if ($d2 < $d1) {
                return null;
            }
            $y = (int)$d1->diff($d2)->y;
            $m = (int)$d1->diff($d2)->m;
            $total = $y * 12 + $m;
            return max(1, $total);
        } catch (\Exception $e) {
            return null;
        }
    }
    public function searchByTitleOrCompany(string $term): array {
        $pdo = Database::getPdo();
        $search = "%" . $term . "%";
        // On cherche dans le titre de l'offre OU dans le nom de l'entreprise
        $sql = "
            SELECT o.*, e.nom AS entreprise_nom
            FROM offre o
            LEFT JOIN entreprise e ON o.id_entreprise = e.id
            WHERE (o.titre LIKE :term OR e.nom LIKE :term)
            ORDER BY o.created_at DESC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':term' => $search]);        
        $result= $stmt->fetchAll();
        return $result;
    }
}
