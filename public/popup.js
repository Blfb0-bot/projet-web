function ouvrir(id) {
    document.getElementById(id).classList.add("actif");
}
function fermer(id) {
    document.getElementById(id).classList.remove("actif");
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
    // On vérifie si l'utilisateur est déjà connecté via une classe sur le body ou une variable JS
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