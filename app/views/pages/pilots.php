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
                        <th>Outils</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pilots)): ?>
                        <?php foreach ($pilots as $pilot): ?>
                            <?php $pid = (int)($pilot['id'] ?? 0); ?>
                            <tr>
                                <td><?= htmlspecialchars((string)($pilot['prenom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string)($pilot['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if ($pid > 0 && isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'pilote')): ?>
                                        <button onclick="ouvrir('popup-modifier-pilote-<?= $pid ?>')">modifier</button>
                                        <button onclick="ouvrir('popup-supprimer-pilote-<?= $pid ?>')">supprimer</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">Aucun pilote trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php foreach (($pilots ?? []) as $p): ?>
                <?php $ppid = (int)($p['id'] ?? 0); ?>
                <?php if ($ppid > 0): ?>
                    <div class="overlay" id="popup-modifier-pilote-<?= $ppid ?>" style="display:none;">
                        <div class="popup">
                            <h2>Modifier <?= htmlspecialchars($p['prenom']) ?></h2>
                            <form action="index.php?controller=pilots&action=update" method="post">
                                <input type="hidden" name="id" value="<?= $ppid ?>">
                                <input type="text" name="edit-prenom" value="<?= htmlspecialchars($p['prenom']) ?>" required>
                                <input type="text" name="edit-nom" value="<?= htmlspecialchars($p['nom']) ?>" required>
                                <button type="submit">Enregistrer</button>
                            </form>
                            <button onclick="fermer('popup-modifier-pilote-<?= $ppid ?>')">Fermer</button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>