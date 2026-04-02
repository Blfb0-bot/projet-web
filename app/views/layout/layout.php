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
                <input type="hidden" name="action" value="<?= htmlspecialchars($_GET['action'] ?? 'index') ?>">
                <input type="search" name="search" placeholder="recherche..." aria-label="recherche sur le site" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <input type="submit" value="Rechercher">
            </form>
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
                <p>efkhvbezrohvezfvbezintrpjnetintpivntrpvib</p>
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
                    <a href="/index.php?controller=profile&action=index">Gérer mon profil</a><br/>
                    <a href="/index.php?controller=auth&action=logout">Déconnexion</a><br/>
                <?php else : ?>
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
                            <label for="email">Email</label><br/>
                            <input type="email" name="email" required><br/>
                            <label for="mot-de-passe">Mot de passe</label><br/>
                            <input type="password" name="mot_de_passe" required><br/><br/>
                            <input type="submit" value="Se connecter">
                        </form>
                    </div>
                    <div class="formulaire-profil" id="inscription">
                        <form action="index.php?controller=auth&action=register" method="post">
                            <h2>Inscription</h2>
                            <input type="text" name="nom" placeholder="Nom" required><br/>
                            <input type="text" name="prenom" placeholder="Prénom" required><br/>
                            <select name="role" required>
                                <option value="etudiant">Étudiant</option>
                                <option value="pilote">Pilote</option>
                                <option value="visiteur">visiteur</option>
                                <option value="admin">admin</option>
                            </select><br/>
                            <input type="email" name="email" placeholder="Email" required><br/>
                            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br/><br/>
                            <input type="submit" value="Créer mon compte">
                        </form>
                    </div>
                <?php endif ?>
                <br/>
                <button onclick="fermer('popup-profil')">Fermer</button>
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
            // On attend que tout soit chargé
            window.addEventListener('load', function() {
                const userIsConnected = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
                const aEteFermee = sessionStorage.getItem('popupManuellementFermee');

                console.log("Connecté:", userIsConnected, "Déjà fermée:", aEteFermee);

                if (!userIsConnected && aEteFermee !== 'true') {
                    ouvrir('popup-profil');
                }
            });
        })();
    </script>
</body>
</html>