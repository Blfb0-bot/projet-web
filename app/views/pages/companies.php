<!--Le contenue de la page-->
<section id="presentation-entreprise">
    <h1>Nos entreprises</h1>
</section>
<section id="outils-entreprise">
    <button id="creation-offre" onclick="ouvrir('popup-creer-entreprise')">créer une entreprise</button>
</section>
<!--Popup création d'une entreprise-->
<div class="overlay" id="popup-creer-entreprise">
    <div class="popup">
        <h2>Creation d'une entreprise</h2>
        <?php if (!empty($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'missing_fields'): ?>
                <p class="form-error">Merci de renseigner tous les champs.</p>
            <?php elseif ($_GET['error'] === 'known_company'): ?>
                <p class="form-error">Cette entreprise existe déjà.</p>
            <?php endif; ?>
        <?php endif; ?>
        <form action="<?= htmlspecialchars($formBase . 'create', ENT_QUOTES, 'UTF-8') ?>" method="post">
            <label for ="create-nom">Nom de l'entreprise</label><br/>
            <input type="text" id="create-nom" name="nom" required maxlength="200" placeholder="Ex. SoftCorp"><br/>
            <label for="create-desc">Description de l'entreprise</label><br/>
            <textarea id="create-desc" name="description" rows="4" required></textarea><br/>
            <label for="create-mail">Mail de l'entreprise</label><br/>
            <input type="email" id="create-mail" name="email" required placeholder="Ex. entreprise@exemple.com"><br/>
            <label for="create-numero">Numéro de l'entreprise</label><br/>
            <input type="number" id="create-numero" name="telephone" required placeholder="Ex. 0123456789"><br/><br/>
            <input type="submit" value="Enregistrer">
            <input type="reset" value="Réinitialiser"><br/><br/>
        </form>
        <button onclick="fermer('popup-creer-entreprise')">Fermer</button>
    </div>
</div>
<section id="nos-entreprises">
    <?php foreach (($companies ?? []) as $company): ?>
        <?php
            $cid = (int)($company['id'] ?? 0);
        ?>
        <div class="entreprises">
            <div class="debut-contenu-entreprise">
                <img src="../public/images/image-entreprise.png" alt="image-entreprise.png">
                <p><?= htmlspecialchars((string)($company['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="contenu-entreprise">
                <article class="description">
                    <p><?= nl2br(htmlspecialchars((string)($company['description'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></p>
                </article>
                <div class="information-entreprise">
                    <p><?= htmlspecialchars((string)($company['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                    <p><?= htmlspecialchars((string)($company['telephone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
            <div class="fin-contenu-entreprise">
                <?php if ($cid > 0): ?> <!--si l'ID de l'entreprise est valide -->
                <div class="modification-entreprise">
                    <button onclick="ouvrir('popup-modifier-entreprise-<?= $cid ?>')">modifier</button>
                    <button onclick="ouvrir('popup-supprimer-entreprise-<?= $cid ?>')">supprimer</button>
                    <p>evaluer</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($cid > 0): ?> <!--si l'ID de l'entreprise est valide -->
            <!-- Popup pour la modification d'entreprise-->
            <div class="overlay" id="popup-modifier-entreprise-<?= $cid ?>">
                <div class="popup">
                    <h2>Modifier l'entreprise</h2>
                    <form action="<?= htmlspecialchars($formBase . 'update', ENT_QUOTES, 'UTF-8') ?>" method="post">
                        <input type="hidden" name="id" value="<?= $cid ?>">
                        <label for="edit-nom-<?= $cid ?>">Nom de l'entreprise</label><br/>
                        <input type="text" id="edit-nom-<?= $cid ?>" name="nom" required maxlength="200" value="<?= htmlspecialchars((string)($company['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><br/>
                        <label for="edit-desc-<?= $cid ?>">Description de l'entreprise</label><br/>
                        <textarea id="edit-desc-<?= $cid ?>" name="description" rows="4" required><?= htmlspecialchars((string)($company['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea><br/>
                        <label for="edit-mail-<?= $cid ?>">Mail de l'entreprise</label><br/>
                        <input type="email" id="edit-mail-<?= $cid ?>" name="email" required value="<?= htmlspecialchars((string)($company['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><br/>
                        <label for="edit-numero-<?= $cid ?>">Numéro de l'entreprise</label><br/>
                        <input type="tel" id="edit-numero-<?= $cid ?>" name="telephone" required value="<?= htmlspecialchars((string)($company['telephone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><br/><br/>
                        <input type="submit" value="Enregistrer">
                        <input type="reset" value="Réinitialiser"><br/><br/>
                    </form>
                    <button onclick="fermer('popup-modifier-entreprise-<?= $cid ?>')">Fermer</button>
                </div>
            </div>
            <!-- Popup pour la suppression d'entreprise-->
            <div class="overlay" id="popup-supprimer-entreprise-<?= $cid ?>">
                <div class="popup">
                    <h2>Supprimer l'entreprise</h2>
                    <p><?=htmlspecialchars((string)($company['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                    <form action="<?= htmlspecialchars($formBase . 'delete', ENT_QUOTES, 'UTF-8') ?>" method="post">
                        <input type="hidden" name="id" value="<?= $cid ?>">
                        <button type="submit">Oui, supprimer</button>
                    </form>
                    <button onclick="fermer('popup-supprimer-entreprise-<?= $cid ?>')">fermer</button>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</section>