<?php $cssExtra = '<link rel="stylesheet" href="/public/styles/offre.css">'; ?>

<?php
ob_start(); // démarre la capture du contenu
?>
<!--Le contenue de la page-->
<section id="presentation-offre">
    <h1>Nos Offres</h1>
</section>
<section id="outils-offre">
    <button id="filtre" onclick="">filtre</button>
    <button id="creation-offre" onclick="ouvrir('popup-creer-offre')">créer une offre</button>
</section>
<section id="nos-offres">
    <div class="offres" >
        <div class="debut-contenu-offre">
            <div class="type" onclick="ouvrir('popup-postuler-offre')">stage</div>
            <div class="title"><h3>Title</h3></div>
            <div><a class="employeur" href="/index.php?controller=entreprise&action=index">entreprises</a></div>
            <div class="modification-offre">
                <button onclick="ouvrir('popup-modifier-offre')">modifier</button>
                <button onclick="ouvrir('popup-supprimer-offre')">supprimer</button>
            </div>
            
        </div>
        <div class="contenu-offre">
            <div class="description">
                <h2>description</h2>
                <article>
                    <p>tetetetetetetettetetetetetetetet</p>
                </article>
            </div>
            <div class="competences">
                <h2>compétences</h2>
                <article>
                    <p>tetetetetetetettetetetetetetetet</p>
                </article>
            </div>
            <div class="detail">
                <article><p>nombre de candidat: </p></article>
                <article><p>rémunération: </p></article>
            </div>
        </div>
        <div class="fin-contenu-offre">
            <p>date et heure</p>
        </div>
    </div>
</section>

<?php
$content   = ob_get_clean();         // stocke le contenu
$pageTitle = "Offre — Web for All"; // titre de l'onglet
require_once 'app/views/layout/layout.php'; // affiche tout
?>