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
    localStorage.setItem('cookiesAcceptes', 'true');
    fermer('popup-cookies');
}

// Cette fonction gère l'affichage prioritaire des cookies sur le profil
function verifierPopups(userIsConnected) {
    const cookiesDejaAcceptes = localStorage.getItem('cookiesAcceptes');
    const aEteFermee = sessionStorage.getItem('popupManuellementFermee');

    if (cookiesDejaAcceptes !== 'true') {
        ouvrir('popup-cookies');
    } else if (!userIsConnected && aEteFermee !== 'true') {
        ouvrir('popup-profil');
    }
}