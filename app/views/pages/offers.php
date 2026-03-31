<?php
$cssExtra = '<link rel="stylesheet" href="/public/styles/offre.css">';
ob_start();
$formBase = '/index.php?controller=offers&action=';
?>

<!--Le contenue de la page-->
<section id="presentation-offre">
    <h1>Nos Offres</h1>
</section>
<section id="outils-offre">
    <button id="creation-offre" onclick="ouvrir('popup-creer-offre')">créer une offre</button>
</section>
<section id="nos-offres">
    <?php foreach (($offers ?? []) as $offer): ?>
        <?php $oid = (int)($offer['id'] ?? 0); ?>
        <div class="offres">
            <div class="debut-contenu-offre">
                <div class="type" onclick="ouvrir('popup-postuler-offre')">offre de stage</div>
                <div class="title"><h3><?= htmlspecialchars((string)($offer['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h3></div>
                <div><a class="évaluer" href="#">évaluer</a></div>
                <?php if ($oid > 0): ?>
                <div class="modification-offre">
                    <button type="button" onclick="ouvrir('popup-modifier-offre-<?= $oid ?>')">modifier</button>
                    <button type="button" onclick="ouvrir('popup-supprimer-offre-<?= $oid ?>')">supprimer</button>
                </div>
                <?php endif; ?>
            </div>
            <div class="contenu-offre">
                <div class="description">
                    <h2>description</h2>
                    <article>
                        <p><?= nl2br(htmlspecialchars((string)($offer['description'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></p>
                    </article>
                </div>
                <div class="competences">
                    <h2>compétences</h2>
                    <article>
                        <p><?= htmlspecialchars((string)($offer['competences'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                    </article>
                </div>
                <div class="detail">
                    <article><p>entreprise: <?= htmlspecialchars((string)($offer['entreprise_nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p></article>
                    <article><p>nombre de candidat: <?= htmlspecialchars((string)($offer['nb_candidatures'] ?? '0'), ENT_QUOTES, 'UTF-8') ?></p></article>
                    <article><p>rémunération: <?= htmlspecialchars((string)($offer['remuneration'] ?? ''), ENT_QUOTES, 'UTF-8') ?> €</p></article>
                </div>
            </div>
            <div class="fin-contenu-offre">
                <p><?= htmlspecialchars((string)($offer['date_fin'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</section>
<?php if (!empty($_GET['error']) && $_GET['error'] === 'missing_fields'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ouvrir === 'function') {
        ouvrir('popup-creer-offre');
    }
});
</script>
<?php endif; ?>

<?php
$content   = ob_get_clean();
$pageTitle = "Offre — Web for All";
require_once ROOT . '/app/views/layout/layout.php';
?>
