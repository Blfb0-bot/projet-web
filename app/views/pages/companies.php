<?php $cssExtra = '<link rel="stylesheet" href="/public/styles/entreprise.css">'; ?>

<?php
ob_start(); // démarre la capture du contenu
?>
<!--Le contenue de la page-->
<section id="presentation-entreprise">
    <h1>Nos entreprises</h1>
</section>
<section id="outils-entreprise">
    <button id="filtre" onclick="">filtre</button>
    <button id="creation-offre" onclick="ouvrir('popup-creer-entreprise')">créer une entreprise</button>
</section>
<section id="nos-entreprises">
    <div class="entreprises">
        <?php foreach (($companies ?? []) as $company): ?>
            <div class="entreprise">
                <div class="debut-contenu-entreprise">
                    <img src="../public/images/image-entreprise.png" alt="image-entreprise.png">
                    <p><?= htmlspecialchars((string)($company['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="contenu-entreprise">
                    <article class="description">
                        <p><?= nl2br(htmlspecialchars((string)($company['description'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></p>
                    </article>
                    <div class="information-entreprise">
                        <p><?= htmlspecialchars((string)($company['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><?= htmlspecialchars((string)($company['telephone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
                <div class="fin-contenu-entreprise">
                    <div class="modification-entreprise">
                        <button onclick="ouvrir('popup-modifier-entreprise')">modifier</button>
                        <button onclick="ouvrir('popup-supprimer-entreprise')">supprimer</button>
                        <p>evaluer</p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php
$content   = ob_get_clean();         // stocke le contenu
$pageTitle = "Entreprise — Web for All"; // titre de l'onglet
require_once 'app/views/layout/layout.php'; // affiche tout
?>