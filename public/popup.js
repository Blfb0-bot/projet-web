formData.append('csrf_token', document.querySelector('meta[name="csrf_token"]').content);
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
(function () {
    const track = document.getElementById('carouselTrack');
    const dotsEl = document.getElementById('carouselDots');
    if (!track || !dotsEl) return;

    let cur = 0;
    const cards = track.querySelectorAll('.stat-card');
    const total = cards.length;

    cards.forEach((_, i) => {
        const btn = document.createElement('button');
        btn.className = 'carousel-dot' + (i === 0 ? ' active' : '');
        btn.onclick = () => goTo(i);
        dotsEl.appendChild(btn);
    });

    function goTo(n) {
        cur = n;
        const cardWidth = track.parentElement.offsetWidth;
        track.style.transform = 'translateX(-' + (cur * cardWidth) + 'px)';
        document.querySelectorAll('.carousel-dot').forEach((d, i) =>
            d.className = 'carousel-dot' + (i === cur ? ' active' : ''));
        document.getElementById('prevBtn').disabled = cur === 0;
        document.getElementById('nextBtn').disabled = cur === total - 1;
    }

    document.getElementById('prevBtn').onclick = () => { if (cur > 0) goTo(cur - 1); };
    document.getElementById('nextBtn').onclick = () => { if (cur < total - 1) goTo(cur + 1); };

    window.addEventListener('resize', () => goTo(cur));
})();
// SF24 — Ajouter
async function ajouterWishlist(offre_id) {
    const formData = new FormData();
    formData.append('offre_id', offre_id);
    formData.append('csrf_token', csrfToken);

    try {
        const res  = await fetch('index.php?controller=wishlist&action=ajouter', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            // Désactive le bouton visuellement
            const btn = document.querySelector(`[data-offre="${offre_id}"]`);
            if (btn) {
                btn.textContent = '❤️ Dans ma wish-list';
                btn.disabled = true;
            }
        } else {
            alert('⚠️ ' + data.error);
        }
    } catch (e) {
        alert('Erreur réseau, réessaie.');
    }
}
// SF25 — Retirer une offre de la wish-list
async function retirerWishlist(offre_id) {
    if (!confirm('Retirer cette offre de ta wish-list ?')) return;

    const formData = new FormData();
    formData.append('offre_id', offre_id);
    formData.append('csrf_token', csrfToken);

    try {
        const res  = await fetch('index.php?controller=wishlist&action=retirer', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            // Supprime la carte sans recharger la page
            document.getElementById(`offre-${offre_id}`)?.remove();

            // Si la wishlist est vide, affiche un message
            const container = document.getElementById('wishlist-container');
            if (container && container.children.length === 0) {
                container.innerHTML = '<p>Ta wish-list est vide. <a href="index.php?controller=offers&action=index">Voir les offres</a></p>';
            }
        } else {
            alert('⚠️ ' + data.error);
        }
    } catch (e) {
        alert('Erreur réseau, réessaie.');
    }
}