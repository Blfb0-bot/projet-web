<!--Le contenue de la page-->
<section id="presentation-etudiant">
    <h1>Nos Etudiants</h1>
</section>
<section id="nos-etudiant">
    <div class="etudiants">
        <div class="table-etudiant">
            <table>
                <thead>
                    <tr>
                        <th>Prenom</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>outils</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php foreach (($students ?? []) as $student): ?>
                            <?php $eeid = (int)($student['id'] ?? 0); ?>
                            <tr>
                                <td><?= htmlspecialchars((string)($student['prenom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string)($student['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string)($student['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if ($eeid > 0): ?><!--si l'ID de l'etudiant est valide -->
                                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'etudiant'|| $_SESSION['user_role'] === 'admin'): ?>
                                            <button onclick="ouvrir('popup-modifier-etudiant-<?= $eeid ?>')">modifier</button>
                                            <button onclick="ouvrir('popup-supprimer-etudiant-<?= $eeid ?>')">supprimer</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Aucun etudiant trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php foreach (($students ?? []) as $student): ?>
                <?php if ($eeid > 0): ?>
                    <div class="overlay" id="popup-modifier-etudiant-<?= $eeid ?>">
                        <div class="popup">
                            <h2>Modifier l'etudiant</h2>
                            <form action="<?= htmlspecialchars($formBase . 'update', ENT_QUOTES, 'UTF-8') ?>" method="post">
                                <input type="hidden" name="id" value="<?= $eeid ?>">
                                <label for="edit-prenom">Prenom</label><br/>
                                <input type="text" id="edit-prenom" name="edit-prenom" required maxlength="100" value="<?= htmlspecialchars((string)($student['prenom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><br/>
                                <label for="edit-nom">Nom</label><br/>
                                <input type="text" id="edit-nom" name="edit-nom" required maxlength="100" value="<?= htmlspecialchars((string)($student['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><br/>
                                <label for="edit-email">Email</label><br/>
                                <input type="email" id="edit-email" name="edit-email" required maxlength="255" value="<?= htmlspecialchars((string)($student['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><br/><br/>
                                <input type="submit" value="Enregistrer">
                                <button onclick="fermer('popup-modifier-etudiant-<?= $eeid ?>')">Annuler</button>
                            </form>
                        </div>
                    </div>
                    <div class="overlay" id="popup-supprimer-etudiant-<?= $eeid ?>">
                        <div class="popup">
                            <h2>Supprimer l'etudiant</h2>
                            <!-- Formulaire de suppression de l'etudiant -->
                            <form action="<?= htmlspecialchars($formBase . 'delete', ENT_QUOTES, 'UTF-8') ?>" method="post">
                                <input type="hidden" name="id" value="<?= $eeid ?>">
                                <p>Êtes-vous sûr de vouloir supprimer cet etudiant ?</p>
                                <button type="submit">oui, supprimer</button>
                                <button onclick="fermer('popup-supprimer-etudiant-<?= $eeid ?>')">Annuler</button>
                            </form>
                        </div>
                    </div>
                <?php endif ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
