<?php $cssExtra = '<link rel="stylesheet" href="/public/styles/acceuil.css">'; ?>

<?php
ob_start(); // démarre la capture du contenu
?>
<!--Le contenue de la page-->
<section id="presentation">
    <h1 id="grand-titre">Qui sommes nous ?</h1>
</section>
<section id="test">
    <p>Notre site est le meilleur au monde, il vous permet de trouver votre stage...</p>
    <br/>
</section>

<?php
$content   = ob_get_clean();         // stocke le contenu
$pageTitle = "Accueil — Web for All"; // titre de l'onglet
require_once 'app/views/layout/layout.php'; // affiche tout
?>