function ouvrir(id) {
    const popup = document.getElementById(id);
    if (popup) {
        popup.classList.add("actif");
        console.log("Ouverture de la popup : " + id);
    } else {
        console.error("ID non trouvé : " + id);
    }
}
function fermer(id) {
    const elem = document.getElementById(id);
    if (elem) {
        elem.classList.remove("actif");
        
        // Si c'est la popup de profil qu'on ferme, on enregistre l'action
        if (id === 'popup-profil') {
            sessionStorage.setItem('popupManuellementFermee', 'true');
        }
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
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');
    if (error === 'missing_fields' || error === 'unknown_company') {
        ouvrir('popup-creer-offre');
    }
});
function accepterCookies() {
    localStorage.setItem('cookiesAcceptees', 'true'); // Vérifie bien l'orthographe ici
    fermer('popup-cookies');
    // Optionnel : recharger la page pour afficher le popup profil immédiatement après
    location.reload(); 
}
function switchOnglet(id) {
    // Cacher tous les onglets
    document.querySelectorAll('.onglet-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.onglet-btn').forEach(el => el.classList.remove('actif-tab'));

    // Afficher l'onglet cible
    document.getElementById(id).style.display = 'block';

    // Activer le bon bouton
    const map = { 'onglet-edit': 'btn-edit', 'onglet-password': 'btn-password', 'onglet-delete': 'btn-delete' };
    document.getElementById(map[id]).classList.add('actif-tab');
}

function checkStrength(v) {
    let score = [v.length >= 8, /[A-Z]/.test(v), /[0-9]/.test(v), /[^A-Za-z0-9]/.test(v)].filter(Boolean).length;
    const niveaux = [
        { w: '0%',   c: 'red',    l: '' },
        { w: '25%',  c: 'red',    l: 'Faible' },
        { w: '50%',  c: 'orange', l: 'Moyen' },
        { w: '75%',  c: 'green',  l: 'Fort' },
        { w: '100%', c: 'green',  l: 'Très fort' }
    ];
    document.getElementById('pw-bar').style.width      = niveaux[score].w;
    document.getElementById('pw-bar').style.background = niveaux[score].c;
    document.getElementById('pw-label').textContent    = niveaux[score].l;
}

function checkConfirm() {
    const ok  = document.getElementById('confirm-del').value === 'SUPPRIMER';
    const btn = document.getElementById('btn-del-submit');
    btn.disabled      = !ok;
    btn.style.opacity = ok ? '1' : '0.4';
    btn.style.cursor  = ok ? 'pointer' : 'not-allowed';
}