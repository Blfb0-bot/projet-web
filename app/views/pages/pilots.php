<!--Le contenue de la page-->
<section id="presentation-pilote">
    <h1>Nos Pilotes</h1>
</section>
<section id="nos-pilote">
    <div class="pilote">
        <div class="table-pilote">
            <table>
                <thead>
                    <tr>
                        <th>Prenom</th>
                        <th>Nom</th>
                        <th>outils</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($pilots ?? []) as $pilot): ?>
                        <?php $pid = (int)($pilot['id'] ?? 0); ?>
                        <tr>
                            <td><?= htmlspecialchars((string)($pilot['prenom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)($pilot['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?php if ($pid > 0): ?><!--si l'ID du pilote est valide -->
                                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pilote'|| $_SESSION['user_role'] === 'admin'): ?>
                                        <button onclick="ouvrir('popup-modifier-pilote-<?= $pid ?>')">modifier</button>
                                        <button onclick="ouvrir('popup-supprimer-pilote-<?= $pid ?>')">supprimer</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                            <?php if ($pid > 0): ?>
                                <div class="overlay" id="popup-modifier-pilote-<?= $pid ?>">
                                    <div class="popup">
                                        <h2>Modifier le pilote</h2>
                                        <form action="<?= htmlspecialchars($formBase . 'update', ENT_QUOTES, 'UTF-8') ?>" method="post">
                                            <input type="hidden" name="id" value="<?= $pid ?>">
                                            <label for="edit-prenom">Prenom</label><br/>
                                            <input type="text" id="edit-prenom" name="edit-prenom" required maxlength="100" value="<?= htmlspecialchars((string)($pilot['prenom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><br/>
                                            <label for="edit-nom">Nom</label><br/>
                                            <input type="text" id="edit-nom" name="edit-nom" required maxlength="100" value="<?= htmlspecialchars((string)($pilot['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><br/><br/>
                                            <input type="submit" value="Enregistrer">
                                            <input type="reset" value="Réinitialiser"><br/><br/>
                                        </form>
                                        <button onclick="fermer('popup-modifier-pilote-<?= $pid ?>')">Fermer</button>
                                    </div>
                                </div>
                                <div class="overlay" id="popup-supprimer-pilote-<?= $pid ?>">
                                    <div class="popup">
                                        <h2>Supprimer le pilote</h2>
                                        <!-- Formulaire de suppression du pilote -->
                                        <form action="<?= htmlspecialchars($formBase . 'delete', ENT_QUOTES, 'UTF-8') ?>" method="post">
                                            <input type="hidden" name="id" value="<?= $pid ?>">
                                            <p>Êtes-vous sûr de vouloir supprimer ce pilote ?</p>
                                            <button type="submit">Oui, supprimer</button>
                                            <button onclick="fermer('popup-supprimer-pilote-<?= $pid ?>')">Annuler</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>