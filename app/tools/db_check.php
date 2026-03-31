<?php
// Script CLI pour vérifier la connexion DB et les données
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Calculer ROOT
$root = realpath(__DIR__ . '/../../');
if ($root === false) {
    echo "Impossible de déterminer le chemin racine.\n";
    exit(1);
}
define('ROOT', $root);

require_once ROOT . '/app/config/EnvLoader.php';
\EnvLoader::load(ROOT . '/.env');
require_once ROOT . '/app/config/Database.php';

try {
    $pdo = Database::getPdo();
} catch (Throwable $e) {
    echo "Échec connexion PDO: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

$tables = ['entreprise', 'offre', 'utilisateur', 'competence'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) AS nb FROM `" . $t . "`");
        $row = $stmt->fetch();
        $nb = $row['nb'] ?? '0';
        echo sprintf("Table %-12s : %s rows\n", $t, $nb);
    } catch (Throwable $e) {
        echo sprintf("Erreur sur table %s : %s\n", $t, $e->getMessage());
    }
}

// Afficher 3 offres sample via la même requête que le modèle
$sql = "SELECT o.id, o.titre, o.description, o.remuneration, o.date_debut, o.date_fin, o.duree_mois, o.created_at, e.nom AS entreprise_nom, (SELECT COUNT(*) FROM candidature ca WHERE ca.id_offre = o.id) AS nb_candidatures, GROUP_CONCAT(DISTINCT c.libelle ORDER BY c.libelle SEPARATOR ', ') AS competences FROM offre o JOIN entreprise e ON o.id_entreprise = e.id LEFT JOIN offre_competence oc ON oc.id_offre = o.id LEFT JOIN competence c ON c.id = oc.id_competence GROUP BY o.id ORDER BY o.created_at DESC LIMIT 3";
try {
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();
    echo "\nExemples d'offres (max 3):\n";
    foreach ($rows as $r) {
        echo "- [" . ($r['id'] ?? '') . "] " . ($r['titre'] ?? '') . " (" . ($r['entreprise_nom'] ?? '') . ")\n";
        echo "  compétences: " . ($r['competences'] ?? '') . "\n";
        echo "  nb candidatures: " . ($r['nb_candidatures'] ?? '0') . "\n";
    }
} catch (Throwable $e) {
    echo "Erreur requête offres: " . $e->getMessage() . PHP_EOL;
}

echo "\nPour lancer: php app/tools/db_check.php\n";
