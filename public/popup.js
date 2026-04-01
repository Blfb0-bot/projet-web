function ouvrir(id) {
    document.getElementById(id).classList.add("actif");
}
function fermer(id) {
    document.getElementById(id).classList.remove("actif");
}
function changerOnglet(id){
    document.querySelectorAll('.formulaire-profil').forEach(function(el){
        el.classList.remove('actif');
    });
    document.getElementById(id).classList.add('actif');
}
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');
    if (error === 'missing_fields' || error === 'unknown_company') {
        ouvrir('popup-creer-offre');
    }
});