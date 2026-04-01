function ouvrir(id) {
    console.log("Tentative d'ouverture de : " + id); // Pour vérifier dans la console
    const elem = document.getElementById(id);
    if (elem) {
        elem.classList.add("actif");
    }
}
function fermer(id) {
    const elem = document.getElementById(id);
    if (elem) {
        elem.classList.remove("actif");
    }
}
function basculerAuth() {
    const isChecked = document.getElementById('toggle-auth').checked;
    const formConnexion = document.getElementById('connexion');
    const formInscription = document.getElementById('inscription');

    if (isChecked) {
        // On bascule sur l'inscription
        formConnexion.classList.remove("actif");
        formInscription.classList.add("actif");
    } else {
        // On revient sur la connexion
        formInscription.classList.remove("actif");
        formConnexion.classList.add("actif");
    }
}
// Ouvrir la popup automatiquement au chargement si pas connecté
window.onload = function() {
    // Si l'utilisateur n'est pas connecté, le JS ajoute la classe 'actif' dynamiquement
    const isConnected = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    if (!isConnected) {
        ouvrir('popup-profil'); 
    }
};
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');
    if (error === 'missing_fields' || error === 'unknown_company') {
        ouvrir('popup-creer-offre');
    }
});