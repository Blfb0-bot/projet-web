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
        <div class="debut-contenu-entreprise">
            <img src="../public/images/image-entreprise.png" alt="image-entreprise.png">
            <p>Nom de l'entreprise</p>
        </div>
        <div class="contenu-entreprise">
            <article class="description">
                <p>description</p>
            </article>
            <div class="information-entreprise">
                <p>mail</p>
                <p>numéro</p>
                <p>blablablablabla</p>
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
</section>

<?php
$content   = ob_get_clean();         // stocke le contenu
$pageTitle = "Entreprise — Web for All"; // titre de l'onglet
require_once 'app/views/layout/layout.php'; // affiche tout
?>