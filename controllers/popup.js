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
