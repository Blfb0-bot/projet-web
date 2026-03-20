<?php $cssExtra = '<link rel="stylesheet" href="/public/styles/pilote.css">'; ?>

<?php
ob_start(); // démarre la capture du contenu
?>
<!--Le contenue de la page-->
<section id="presentation-pilote">
    <h1>Nos Pilotes</h1>
</section>
<section id="outils-pilote">
    <button id="creation-offre" onclick="ouvrir('popup-creer-pilote')">créer un pilote</button>
</section>
<section id="nos-pilote">
    <div class="pilote">
        <div class="table-pilote">
            <table>
                <thead>
                        <tr>
                    <th>Prenom</td>
                    <th>Nom</td>
                    <th>outils</td>
                </tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Aurélien</td>
                    <td>Lefebvre</td>
                    <td><button onclick="ouvrir('popup-modifier-pilote')">modifier</button>
                <button onclick="ouvrir('popup-supprimer-pilote')">supprimer</button></td>
                </tr>
                </tbody>
</section>

<?php
$content   = ob_get_clean();         // stocke le contenu
$pageTitle = "Pilote — Web for All"; // titre de l'onglet
require_once 'app/views/layout/layout.php'; // affiche tout
?>