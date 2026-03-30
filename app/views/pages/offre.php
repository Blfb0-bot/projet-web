<?php $cssExtra = '<link rel="stylesheet" href="/public/styles/offre.css">'; ?>

<?php
ob_start(); // démarre la capture du contenu
?>
<!--Le contenue de la page-->
<section id="presentation-offre">
    <h1>Nos Offres de stage</h1>
</section>
<section id="outils-offre">
    <button id="filtre" onclick="">filtre</button>
    <button id="creation-offre" onclick="ouvrir('popup-creer-offre')">créer une offre</button>
</section>
<?php if (empty($offres)) : ?>
    <p>Aucune offre de stage disponible pour le moment.</p>
<?php else : ?>
    <?php foreach ($offres as $offre) : ?>
        <section id="nos-offres">
            <div class="offres" >
                <div class="debut-contenu-offre">
                    <div class="type" onclick="ouvrir('popup-postuler-offre')">stage</div>
                    <div class="title"><h3><?= htmlspecialchars($offre['titre']) ?></h3></div>
                    <div class="employeur"><?= htmlspecialchars($offre['nom_entreprise']) ?></div>
                    <div class="modification-offre">
                        <button onclick="ouvrir('popup-modifier-offre')">modifier</button>
                        <button onclick="ouvrir('popup-supprimer-offre')">supprimer</button>
                    </div>
                    
                </div>
                <div class="contenu-offre">
                    <div class="description">
                        <h2>description</h2>
                        <article>
                            <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
                        </article>
                    </div>
                    <div class="competences">
                        <h2>compétences</h2>
                        <article>
                            <p><?= nl2br(htmlspecialchars($offre['competences'])) ?></p>
                        </article>
                    </div>
                    <div class="detail">
                        <article><p>nombre de candidat: <?= htmlspecialchars($offre['nombre_candidats']) ?></p></article>
                        <article><p>rémunération: <?= number_format($offre['remuneration'], 2, ',', ' ') ?> €</p></article>
                    </div>
                </div>
                <div class="fin-contenu-offre">
                    <p><?= htmlspecialchars($offre['duree']) ?></p>
                </div>
            </div>
        </section>
    <?php endforeach; ?>
<?php endif; ?>

<?php
$content   = ob_get_clean();         // stocke le contenu
$pageTitle = "Offre — Web for All"; // titre de l'onglet
require_once ROOT .'app/views/layout/layout.php'; // affiche tout
?>