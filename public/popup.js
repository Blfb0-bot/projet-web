function ouvrir(id) {
    document.getElementById(id).classList.add("actif");
}
function fermer(id) {
    document.getElementById(id).classList.remove("actif");
}
function basculerAuth() {
    const isChecked = document.getElementById('toggle-auth').checked;
    const formConnexion = document.getElementById('form-connexion');
    const formInscription = document.getElementById('form-inscription');

    if (isChecked) {
        formConnexion.style.display = 'none';
        formInscription.style.display = 'block';
    } else {
        formConnexion.style.display = 'block';
        formInscription.style.display = 'none';
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