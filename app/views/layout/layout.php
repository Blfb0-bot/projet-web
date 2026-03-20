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
    <!--En tête de nos pages-->
    <header id="en-tete">
        <h3>Bienvenue sur Web for All</h3>
        <div id="recherche">
            <form action="/recherche" methode="post">
                <input type="search" name="q" placeholder="recherche..." aria-label="recherche sur le site">
            </form>
        </div>
        <div id="profil">
            <a href="#" onclick="ouvrir('popup-profil')">
                <img src="../public/images/PROFIL.png" alt="PROFIL.png">
            </a>
        </div>
    </header> 
    <!--Barre latérale de nos pages-->
    <aside id="sidebar">
        <div id="logo">
            <a href="../views/acceuil.html">
                <img src="../public/images/LOGO.png" alt="LOGO.png">
            </a>
        </div> 
        <nav id="navigation">
            <a id="offre" href="../views/offre.html">offres</a>
            <a id="entreprise" href="../views/entreprise.html">entreprises</a>
            <a id="etudiant" href="../views/etudiant.html">Etudiants</a>
            <a id="pilote" href="../views/pilote.html">Pilotes</a>
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
                <form action="sumit_message" methode="post"> <!--A voir lequel utiliser-->
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
                <button onclick="changerOnglet('connexion')">Se connecter</button>
                <button onclick="changerOnglet('inscription')">S'inscrire</button>
                <!--formuaire de connexion-->
                <div class="formulaire-profil" id="connexion">
                    <form action="submit_connexion" method="post"> <!--voir qu'elle méthode garder-->
                        <h2>formulaire de connexion</h2>
                        <label for="email">Email</label><br/>
                        <input type="email" id="email" name="email" required><br/>
                        <label for="mot-de-passe">Mot de passe</label><br/>
                        <input type="password" id="mot-de-passe" name="mot-de-passe" required><br/><br/>
                        <input type="submit" value="Envoyer">
                        <input type="reset" value="Réinitialiser"><br/>
                    </form>
                </div>
                <!--formuaire d'inscription-->
                <div class="formulaire-profil" id="inscription">
                    <form action="submit_inscription" method="post"> <!--voir qu'elle méthode garder-->
                        <h2>formulaire d'inscription</h2>
                        <select id="genre" name="genre" required>
                            <option value="Homme">Homme</option>
                            <option value="Femme">Femme</option>
                            <option value="Autre">Autre</option>
                        </select><br/>
                        <label for="nom-utilisateur">Nom de l'utilisateur</label><br/>
                        <input type="text" id="nom-uttilisateur" name="nom-utilisateur" required><br/>
                        <label for="prenom-utilisateur">Prénom de l'utilisateur</label><br/>
                        <input type="text" id="prenom-uttilisateur" name="prenom-utilisateur" required><br/>
                        <label for="email">Email</label><br/>
                        <input type="email" id="email" name="email" required><br/>
                        <label for="mot-de-passe">Mot de passe</label><br/>
                        <input type="password" id="mot-de-passe" name="mot-de-passe" required><br/><br/>
                        <input type="submit" value="Envoyer">
                        <input type="reset" value="Réinitialiser"><br/>
                    </form>
                </div>
                <br/>
                <button onclick="fermer('popup-profil')">Fermer</button>
            </div>
        </div> 
    </div>
    <!--Notre page-->
    <main id="page">
        <?= $content ?>  <!-- contenu unique de la page -->
    </main>
</body>
</html>