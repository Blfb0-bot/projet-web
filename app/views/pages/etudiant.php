<?php $cssExtra = '<link rel="stylesheet" href="/public/styles/etudiant.css">'; ?>

<?php
ob_start(); // démarre la capture du contenu
?>
<!--Le contenue de la page-->
<section id="presentation-etudiant">
    <h1>Nos Etudiants</h1>
</section>
<section id="outils-etudiant">
    <button id="creation-offre" onclick="ouvrir('popup-creer-etudiant')">créer un etudiant</button>
</section>
<section id="nos-etudiant">
    <div class="etudiants">
        <div class="table-etudiant">
            <table>
                <thead>
                    <tr>
                    <th>Prenom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>outils</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Benjamin</td>
                    <td>Lefebvre</td>
                    <td>benjaminlefevre@gmail.com</td>
                    <td><button onclick="ouvrir('popup-modifier-etudiant')">modifier</button>
                    <button onclick="ouvrir('popup-supprimer-etudiant')">supprimer</button></td>
                </tr>
                </tbody>
</section>

<?php
$content   = ob_get_clean();         // stocke le contenu
$pageTitle = "Etudiant — Web for All"; // titre de l'onglet
require_once 'app/views/layout/layout.php'; // affiche tout
?>