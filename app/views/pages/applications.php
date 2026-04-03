<?php
$isPilot  = isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['pilote', 'admin']);
$pilotes  = $pilotes ?? [];
$user     = (new UserModel()) ->getUserById((int)$_SESSION['user_id']);
$hasPilot = !empty($user['id_pilote']);
?>

<section id="header-applications">
    <h1><?= $isPilot ? 'Applications de mes élèves' : 'Mes applications' ?></h1>
</section>

<?php if (!$isPilot): ?>
<section id="pilot-select">
    <?php if ($hasPilot): ?>
        <?php
            $piloteActuel = array_filter($pilotes, fn($p) => (int)$p['id'] === (int)$user['id_pilote']);
            $piloteActuel = reset($piloteActuel);
        ?>
        <p class="pilot-current">
            Votre pilote actuel :
            <strong><?= htmlspecialchars($piloteActuel['prenom'] . ' ' . $piloteActuel['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
        </p>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'pilot_assigned'): ?>
        <p class="form-success">Pilote assigné avec succès.</p>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_pilot'): ?>
        <p class="form-error">Pilote invalide.</p>
    <?php endif; ?>

    <form action="/index.php?controller=applications&action=assignPilot" method="post" class="pilot-form">
        <label for="id_pilote"><?= $hasPilot ? 'Changer de pilote' : 'Choisir votre pilote' ?></label>
        <div class="pilot-list">
            <?php foreach ($pilotes as $p): ?>
                <label class="pilot-item <?= (int)$user['id_pilote'] === (int)$p['id'] ? 'pilot-selected' : '' ?>">
                    <input
                        type="radio"
                        name="id_pilote"
                        value="<?= (int)$p['id'] ?>"
                        <?= (int)$user['id_pilote'] === (int)$p['id'] ? 'checked' : '' ?>
                    >
                    <span><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom'], ENT_QUOTES, 'UTF-8') ?></span>
                    <small><?= htmlspecialchars($p['email'], ENT_QUOTES, 'UTF-8') ?></small>
                </label>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn-assign">Confirmer</button>
    </form>
</section>
<?php endif; ?>

<section id="list-applications">
    <?php if (empty($applications)): ?>
        <p class="empty-state">Aucune application trouvée.</p>
    <?php else: ?>
        <?php foreach ($applications as $app): ?>
            <div class="application-card">
                <div class="app-header">
                    <div>
                        <span class="app-tag">offre de stage</span>
                        <h3><?= htmlspecialchars($app['offre_titre'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="app-entreprise"><?= htmlspecialchars($app['entreprise_nom'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="app-meta">
                        <?php if ($isPilot): ?>
                            <p class="app-student">
                                <?= htmlspecialchars($app['student_prenom'] . ' ' . $app['student_nom'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="app-email"><?= htmlspecialchars($app['student_email'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                        <p class="app-date">
                            Postulé le <?= htmlspecialchars(date('d/m/Y', strtotime($app['date_candidature'])), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                </div>
                <div class="app-body">
                    <div class="app-lm">
                        <h4>Lettre de motivation</h4>
                        <p><?= nl2br(htmlspecialchars($app['lettre_motivation'], ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                    <div class="app-cv">
                        <h4>CV</h4>
                        <?php if ($app['cv_path']): ?>
                            <a class="btn-cv" href="/index.php?controller=applications&action=cv&id=<?= $app['id'] ?>" target="_blank">Voir le CV</a>
                        <?php else: ?>
                            <p class="no-cv">Aucun CV fourni</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>