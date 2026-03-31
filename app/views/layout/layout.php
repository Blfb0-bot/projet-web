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
            <a href="/index.php?controller=accueil&action=index">
                <img src="../public/images/LOGO.png" alt="LOGO.png">
            </a>
        </div> 
        <nav id="navigation">
            <a id="offre" href="/index.php?controller=offers&action=index">offres</a>
            <!--index.php?controller=contact-->
            <a id="entreprise" href="/index.php?controller=companies&action=index">entreprise</a>
            <a id="etudiant" href="/index.php?controller=students&action=index">etudiant</a>
            <a id="pilote" href="/index.php?controller=pilots&action=index">pilote</a>
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
    <!--Popup spécifiques à certaines pages-->
    <div id = "popup-specific">
        <!-- Popup creation-->
        <div id="popup-creation">
            <!-- Popup pour la création d'offre-->
            <div class="overlay" id="popup-creer-offre">
                <div class="popup">
                    <h2>Création d'une offre</h2>
                    <?php if (!empty($_GET['error'])): ?>
                        <?php if ($_GET['error'] === 'missing_fields'): ?>
                            <p class="form-error">Merci de renseigner le nom de l'entreprise, le titre et la description.</p>
                        <?php elseif ($_GET['error'] === 'unknown_company'): ?>
                            <p class="form-error">Entreprise inconnue, veuillez la créer.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                    <form action="<?= htmlspecialchars($formBase . 'create', ENT_QUOTES, 'UTF-8') ?>" method="post">
                        <label for="create-entreprise">Nom de l'entreprise</label><br/>
                        <input type="text" id="create-entreprise" name="entreprise_nom" required maxlength="200" placeholder="Ex. SoftCorp"><br/>
                        <label for="create-titre">Titre de l'offre</label><br/>
                        <input type="text" id="create-titre" name="titre" required><br/>
                        <label for="create-description">Description</label><br/>
                        <textarea id="create-description" name="description" rows="4" cols="40" required></textarea><br/>
                        <label for="create-competences">Compétences (séparées par des virgules)</label><br/>
                        <textarea id="create-competences" name="competences" rows="3" cols="40" placeholder="PHP, MySQL, …"></textarea><br/>
                        <label for="create-remuneration">Rémunération (€)</label><br/>
                        <input type="number" id="create-remuneration" name="remuneration" step="0.01" min="0"><br/>
                        <label for="create-date-debut">Date de début</label><br/>
                        <input type="date" id="create-date-debut" name="date_debut"><br/>
                        <label for="create-date-fin">Date de fin</label><br/>
                        <input type="date" id="create-date-fin" name="date_fin"><br/><br/>
                        <input type="submit" value="Enregistrer">
                        <input type="reset" value="Réinitialiser"><br/><br/>
                    </form>
                    <button type="button" onclick="fermer('popup-creer-offre')">Fermer</button>
                    <?php foreach (($offers ?? []) as $offer):
                    $oid = (int)($offer['id'] ?? 0);
                    if ($oid <= 0) {
                        continue;
                    }
                    $rem = $offer['remuneration'];
                    $remStr = $rem !== null && $rem !== '' ? htmlspecialchars((string)$rem, ENT_QUOTES, 'UTF-8') : '';
                    $dd = $offer['date_debut'] ?? '';
                    $df = $offer['date_fin'] ?? '';
                    $entrepriseNom = (string)($offer['entreprise_nom'] ?? '');
                    ?>
                </div>
            </div>
            <!-- Popup pour la création d'entreprise-->
            <div class="overlay" id="popup-creer-entreprise">
                <div class="popup">
                    <h3>Creation d'une entreprise</h3>
                    <form action="submit_entreprise" methode="post">
                        <label for="image">image de l'entreprise:</label>
                        <input type="image" id="image" name="image" required><br/>
                        <label for="nom">Nom de l'entreprise: </label>
                        <input type="text" id="nom" name="nom" required><br/>
                        <label for="description">Description de l'entreprise: </label><br/>
                        <textarea id="description" name="description" rows="4" cols="40" placeholder="Votre description..." required></textarea><br/>
                        <label for="mail">Mail de l'entreprise: </label>
                        <input type="email" id="mail" name="mail" required><br/>
                        <label for="numero">Numéro de l'entreprise: </label>
                        <input type="number" id="numero" name="numero" required><br/><br/>
                        <input type="submit" value="Envoyer">
                        <input type="reset" value="Réinitialiser"><br/><br/>
                    </form>
                    <button onclick="fermer('popup-creer-entreprise')">Fermer</button>
                </div>
            </div>
            <!-- Popup pour la création d'etudiant-->
            <div class="overlay" id="popup-creer-etudiant">
                <div class="popup">
                    <h2>Creation d'un étudiant</h2>
                    <form action="submit_etudiant" methode="post">
                        <label for="prenom">Prenom de l'étudiant</label>
                        <input type="text" id="prenom" name="prenom" required><br/>
                        <label for="nom">Nom de l'étudiant</label>
                        <input type="text" id="nom" name="nom"><br/>
                        <label for="mail">Email de l'étudiant</label>
                        <input type="email" id="mail" name="mail" required><br/><br/>
                        <input type="submit" value="Envoyer">
                        <input type="reset" value="Réinitialiser"><br/><br/>
                    </form>
                    <button onclick="fermer('popup-creer-etudiant')">Fermer</button>
                </div>
            </div>
            <!-- Popup pour la création de pilote-->
            <div class="overlay" id="popup-creer-pilote">
                <div class="popup">
                    <h2>Creation d'un pilote</h2>
                    <form action="submit_pilote" methode="post">
                        <label for="prenom">Prenom du pilote</label>
                        <input type="text" id="prenom" name="prenom" required><br/>
                        <label for="nom">Nom du pilote</label>
                        <input type="text" id="nom" name="nom"><br/><br/>
                        <input type="submit" value="Envoyer">
                        <input type="reset" value="Réinitialiser"><br/><br/>
                    <button onclick="fermer('popup-creer-pilote')">Fermer</button>
                </div>
            </div>
        </div>
        <!-- Popup modification-->
        <div id ="popup-modification">
            <!-- Popup pour la modification d'offre-->
            <div class="overlay" id="popup-modifier-offre-<?= $oid ?>">
                <div class="popup">
                    <h2>Modifier l'offre</h2>
                    <form action="<?= htmlspecialchars($formBase . 'update', ENT_QUOTES, 'UTF-8') ?>" method="post">
                        <input type="hidden" name="id" value="<?= $oid ?>">
                        <label for="edit-entreprise-<?= $oid ?>">Nom de l'entreprise</label><br/>
                        <input type="text" id="edit-entreprise-<?= $oid ?>" name="entreprise_nom" required maxlength="200" value="<?= htmlspecialchars($entrepriseNom, ENT_QUOTES, 'UTF-8') ?>"><br/>
                        <label for="edit-titre-<?= $oid ?>">Titre</label><br/>
                        <input type="text" id="edit-titre-<?= $oid ?>" name="titre" required value="<?= htmlspecialchars((string)($offer['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><br/>
                        <label for="edit-desc-<?= $oid ?>">Description</label><br/>
                        <textarea id="edit-desc-<?= $oid ?>" name="description" rows="4" cols="40" required><?= htmlspecialchars((string)($offer['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea><br/>
                        <label for="edit-comp-<?= $oid ?>">Compétences (virgules)</label><br/>
                        <textarea id="edit-comp-<?= $oid ?>" name="competences" rows="3" cols="40"><?= htmlspecialchars((string)($offer['competences'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea><br/>
                        <label for="edit-rem-<?= $oid ?>">Rémunération (€)</label><br/>
                        <input type="number" id="edit-rem-<?= $oid ?>" name="remuneration" step="0.01" min="0" value="<?= $remStr ?>"><br/>
                        <label for="edit-dd-<?= $oid ?>">Date de début</label><br/>
                        <input type="date" id="edit-dd-<?= $oid ?>" name="date_debut" value="<?= htmlspecialchars((string)$dd, ENT_QUOTES, 'UTF-8') ?>"><br/>
                        <label for="edit-df-<?= $oid ?>">Date de fin</label><br/>
                        <input type="date" id="edit-df-<?= $oid ?>" name="date_fin" value="<?= htmlspecialchars((string)$df, ENT_QUOTES, 'UTF-8') ?>"><br/><br/>
                        <input type="submit" value="Enregistrer les modifications">
                    </form>
                    <button type="button" onclick="fermer('popup-modifier-offre-<?= $oid ?>')">Fermer</button>
                </div>
            </div>
            <!-- Popup pour la modification d'entreprise-->
            <div class="overlay" id="popup-modifier-entreprise">
                <div class="popup">
                    <button onclick="fermer('popup-modifier-entreprise')">Fermer</button>
                </div>
            </div>
            <!-- Popup pour la modification d'etudiant-->
            <div class="overlay" id="popup-modifier-etudiant">
                <div class="popup">
                    <button onclick="fermer('popup-modifier-etudiant')">Fermer</button>
                </div>
            </div>
            <!-- Popup pour la modification de pilote-->
            <div class="overlay" id="popup-modifier-pilote">
                <div class="popup">
                    <button onclick="fermer('popup-modifier-pilote')">Fermer</button>
                </div>
            </div>    
        </div>
        <!-- Popup suppression-->
        <div id="popup-suppression">
            <!-- Popup pour la suppression d'offre-->
            <div class="overlay" id="popup-supprimer-offre-<?= $oid ?>">
                <div class="popup">
                    <h2>Supprimer cette offre ?</h2>
                    <p>Offre : <?= htmlspecialchars((string)($offer['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                    <form action="<?= htmlspecialchars($formBase . 'delete', ENT_QUOTES, 'UTF-8') ?>" method="post">
                        <input type="hidden" name="id" value="<?= $oid ?>">
                        <button type="submit">Oui, supprimer</button>
                    </form>
                    <button type="button" onclick="fermer('popup-supprimer-offre-<?= $oid ?>')">Annuler</button>
                </div>
            </div>
            <?php endforeach; ?>
            <!-- Popup pour la suppression d'entreprise-->
            <div class="overlay" id="popup-supprimer-entreprise">
                <div class="popup">
                    <button onclick="fermer('popup-supprimer-entreprise')">Fermer</button>
                </div>
            </div>
            <!-- Popup pour la suppression d'etudiant-->
            <div class="overlay" id="popup-supprimer-etudiant">
                <div class="popup">
                    <button onclick="fermer('popup-supprimer-etudiant')">Fermer</button>
                </div>
            </div>
            <!-- Popup pour la supression de pilote-->
            <div class="overlay" id="popup-supprimer-pilote">
                <div class="popup">
                    <button onclick="fermer('popup-supprimer-pilote')">Fermer</button>
                </div>
            </div>
        </div>

        <!--Poppup pour postuler-->
        <div class="overlay" id="popup-postuler-offre">
            <div class="popup">
                <h2>Postuler a cette offre</h2>
                <button onclick="fermer('popup-postuler-offre')">Fermer</button>
            </div>
        </div>
    </div>
    <!--Notre page-->
    <main id="page">
        <?php if (isset($page)) && file_exists($page)) include $page; else echo '<p>Page introuvable</p>'; ?>
    </main>
</body>
</html>