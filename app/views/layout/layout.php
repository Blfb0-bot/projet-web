<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!--sans ça, les mobiles affichent la version pc-->
    <title>WEB FOR ALL</title>
    <link rel="stylesheet" href="../public/styles/layout.css">
    <script src="../public/popup.js"></script>
    <?= $cssExtra ?? '' ?>
</head>
<body>
    <!--Fond d'écran-->
    <video autoplay muted loop id="bg-video">
        <source src="../public/images/background.mp4" type="video/mp4">
    </video>
    <!--En tête de nos pages-->
    <header id="en-tete">
        <h3>Bienvenue sur Web for All</h3>
        <div id="recherche">
            <form action="index.php" method="get">
                <input type="hidden" name="controller" value="<?= htmlspecialchars($_GET['controller'] ?? 'accueil') ?>">
                <input type="hidden" name="action" value="index">
                <input type="search" name="search" placeholder="recherche..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit" style="display:none"></button> </form>
        </div>
        <div id="profil">
            <img onclick="ouvrir('popup-profil')" src="../public/images/PROFIL.png" alt="PROFIL.png">
        </div>
    </header> 
    <!--Barre latérale de nos pages-->
    <aside id="sidebar">
        <div id="logo">
            <a href="/index.php?controller=accueil&action=index">
                <img src="../public/images/LOGO.png" alt="LOGO.png">
            </a>
        </div> 
        <nav id="navigation">
            <a id="offre" href="/index.php?controller=offers&action=index">offres</a>
            <!--index.php?controller=contact-->
            <a id="entreprise" href="/index.php?controller=companies&action=index">entreprise</a>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'etudiant' || $_SESSION['user_role'] === 'pilote'|| $_SESSION['user_role'] === 'admin'): ?>
                <a id="etudiant" href="/index.php?controller=students&action=index">etudiant</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pilote'|| $_SESSION['user_role'] === 'admin'): ?>
                <a id="pilote" href="/index.php?controller=pilots&action=index">pilote</a>
            <?php endif; ?>
        </nav>
        <footer id="pied-de-page">
            <a id="mention-legale" href="#" onclick="ouvrir('popup-mention-legale')">@2026-mentions legales</a>
            <br/><br/>
            <a id="contact" href="#" onclick="ouvrir('popup-contact')">Nous contacter</a>
        </footer>
    </aside>
    <!--Popup principaux de nos pages-->
    <div id="popup-commun">
        <!-- Popup pour les mentions légales-->
        <div class="overlay" id="popup-mention-legale">
            <div class="popup">
                <h2>Bienvenue sur les mentions legales</h2>
                <p>Ce projet a été développé par une équipe d'étudiants de CESI Arras. Pour toute question, rendez-vous dans l'onglet <strong>Contactez-nous</strong></p><br/>
                <p>Ce site est hébergé sur un serveur Apache local</p><br/>
                <p>L'ensemble des contenus présents sur ce site sont la propriété de leurs auteurs. Toute reproduction sans autorisation, même partielle, est interdite.</p><br/>
                <p>Les données personelles traité par Web4all (nom , mail , CV...) ne peuvent etre consultè uniquement par un administrateur</p><br/>
                <p>Les contenus publiés sont fournis par les utilisateurs. L'équipe Web4all ne peut être tenue responsable d'éventuelles inexactitudes et se réserve le droit de supprimer tout contenu inapproprié.</p><br/>
                <button onclick="fermer('popup-mention-legale')">Fermer</button>
            </div>
        </div>
        <!-- Popup pour nous contacter-->
        <div class="overlay" id="popup-contact">
            <div class="popup">
                <h2>Nous sommes à votre écoute</h2>
                <p>Notre numéro: 00-00-00-00-00</p>
                <p>Notre adresse mail: cesi@viacesi.fr</p>
                <p>Notre adresse postale: 7 rue diderot 62000 Arras</p>
                <form action="sumit_message" method="post"> <!--A voir lequel utiliser-->
                    <label for="nom-utilisateur">Nom et prénom de l'utilisateur</label><br/>
                    <input type="text" id="nom-utilisateur" name="nom-utilisateur" required><br/>
                    <label for="message">Vous avez la parole:</label><br/>
                    <textarea id="message" name="message" rows="4" cols="40" placeholder="Votre message..." required ></textarea><br><br>
                    <input type="submit" value="Envoyer">
                    <input type="reset" value="Réinitialiser"><br/>
                </form>
                <button onclick="fermer('popup-contact')">Fermer</button>
            </div>
        </div>
        <!-- Popup pour le profil-->
        <div class="overlay" id="popup-profil">
            <div class="popup">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <h2>Mon Compte</h2>
                    <p>Bonjour, <?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?></p>
                    <p>Rôle : <?= htmlspecialchars($_SESSION['user_role']) ?></p>
                    <hr>

                    <!-- Onglets -->
                    <div style="display:flex; gap:0; margin:1rem 0 0; border-bottom:1px solid #ccc;">
                        <button onclick="switchOnglet('onglet-edit')"   id="btn-edit"     class="onglet-btn actif-tab">Modifier</button>
                        <button onclick="switchOnglet('onglet-password')" id="btn-password" class="onglet-btn">Mot de passe</button>
                        <button onclick="switchOnglet('onglet-delete')" id="btn-delete"   class="onglet-btn danger-tab">Supprimer</button>
                    </div>

                    <!-- Onglet : Modifier profil -->
                    <div id="onglet-edit" class="onglet-content">
                        <form action="index.php?controller=auth&action=profil" method="post">
                            <input type="hidden" name="action_type" value="modifier">
                            <label>Prénom</label><br/>
                            <input type="text" name="prenom" value="<?= htmlspecialchars($_SESSION['user_prenom'] ?? '') ?>" required><br/>
                            <label>Nom</label><br/>
                            <input type="text" name="nom" value="<?= htmlspecialchars($_SESSION['user_nom'] ?? '') ?>" required><br/>
                            <label>Email</label><br/>
                            <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>" required><br/><br/>
                            <input type="submit" value="Enregistrer">
                        </form>
                    </div>

                    <!-- Onglet : Mot de passe -->
                    <div id="onglet-password" class="onglet-content" style="display:none;">
                        <form action="index.php?controller=auth&action=profil" method="post">
                            <input type="hidden" name="action_type" value="password">
                            <label>Mot de passe actuel</label><br/>
                            <input type="password" name="password_actuel" required><br/>
                            <label>Nouveau mot de passe</label><br/>
                            <input type="password" name="password_nouveau" id="pw-new" oninput="checkStrength(this.value)" required><br/>
                            <div style="height:6px; border-radius:3px; background:#eee; margin:4px 0;">
                                <div id="pw-bar" style="height:100%; width:0%; border-radius:3px; background:red; transition:width .2s,background .2s;"></div>
                            </div>
                            <small id="pw-label" style="color:#888;"></small><br/>
                            <label>Confirmer le nouveau mot de passe</label><br/>
                            <input type="password" name="password_confirm" required><br/><br/>
                            <input type="submit" value="Mettre à jour">
                        </form>
                    </div>

                    <!-- Onglet : Supprimer compte -->
                    <div id="onglet-delete" class="onglet-content" style="display:none;">
                        <p style="color:red; font-weight:bold;">⚠ Action irréversible — toutes vos données seront supprimées.</p>
                        <form action="index.php?controller=auth&action=profil" method="post">
                            <input type="hidden" name="action_type" value="supprimer">
                            <label>Tapez <strong>SUPPRIMER</strong> pour confirmer</label><br/>
                            <input type="text" name="confirm_suppression" id="confirm-del" oninput="checkConfirm()" placeholder="SUPPRIMER"><br/><br/>
                            <input type="submit" id="btn-del-submit" value="Supprimer mon compte" disabled
                                style="background:red; color:white; opacity:0.4; cursor:not-allowed;">
                        </form>
                    </div>

                    <br/>
                    <a href="/index.php?controller=auth&action=logout">Déconnexion</a><br/>

                <?php else : ?>
                    <!-- Connexion / Inscription (inchangé) -->
                    <div class="switch-container">
                        <span>Connexion</span>
                        <label class="switch">
                            <input type="checkbox" id="toggle-auth" onchange="basculerAuth()">
                            <span class="slider"></span>
                        </label>
                        <span>Inscription</span>
                    </div>
                    <div class="formulaire-profil actif" id="connexion">
                        <form action="index.php?controller=auth&action=login" method="post">
                            <h2>Connexion</h2>
                            <label>Email</label><br/>
                            <input type="email" name="email" required><br/>
                            <label>Mot de passe</label><br/>
                            <input type="password" name="mot_de_passe" required><br/><br/>
                            <input type="submit" value="Se connecter">
                        </form>
                    </div>
                    <div class="formulaire-profil" id="inscription">
                        <form action="index.php?controller=auth&action=register" method="post">
                            <h2>Inscription</h2>
                            <input type="text"     name="nom"          placeholder="Nom"      required><br/>
                            <input type="text"     name="prenom"       placeholder="Prénom"   required><br/>
                            <select name="role" required>
                                <option value="etudiant">Étudiant</option>
                                <option value="pilote">Pilote</option>
                                <option value="visiteur">Visiteur</option>
                                <option value="admin">Admin</option>
                            </select><br/>
                            <input type="email"    name="email"        placeholder="Email"    required><br/>
                            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br/><br/>
                            <input type="submit" value="Créer mon compte">
                        </form>
                    </div>
                <?php endif ?>

                <br/>
                <button onclick="fermer('popup-profil')">Fermer</button>
            </div>
        </div>
        <div class="overlay" id="popup-cookies">
            <div class="popup">
                <h2>🍪 Politique des cookies</h2>
                <p>Nous utilisons des cookies pour sécuriser votre connexion et améliorer votre navigation en HTTPS.</p>
                <br/>
                <p>En poursuivant, vous acceptez l'utilisation de ces traceurs.</p>
                <br/>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button onclick="accepterCookies()" style="background-color: #2ecc71; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Accepter</button>
                    <button onclick="fermer('popup-cookies')" style="background-color: #e74c3c; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Refuser</button>
                </div>
            </div>
        </div>
    </div>
    <!--Notre page-->
    <main id="page">
        <?php
        if (isset($page) && file_exists($page)) {
            include $page;
        } else {
            echo '<p>Page introuvable</p>';
        }
        ?>
    </main>
    <script>
        (function() {
            window.addEventListener('load', function() {
                const userIsConnected = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
                const aEteFermee = sessionStorage.getItem('popupManuellementFermee');
                const cookiesAcceptees = localStorage.getItem('cookiesAcceptees');

                console.log("Connecté:", userIsConnected, "Cookies:", cookiesAcceptees);

                // 1. PRIORITÉ : On vérifie les cookies d'abord
                if (cookiesAcceptees !== 'true') {
                    console.log("Ouverture cookies (prioritaire)");
                    ouvrir('popup-cookies');
                } 
                // 2. Si les cookies sont OK, on vérifie si on doit afficher le profil
                else if (!userIsConnected && aEteFermee !== 'true') {
                    console.log("Ouverture profil (cookies déjà OK)");
                    ouvrir('popup-profil');
                } 
                else {
                    console.log("Rien à afficher : cookies OK et utilisateur connecté ou popup déjà fermée.");
                }
            });
        })();
    </script>
</body>
</html>