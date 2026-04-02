<?php
$isPilot = isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['pilote', 'admin']);
?>

<section id="header-applications">
    <h1><?= $isPilot ? 'Applications de mes élèves' : 'Mes applications' ?></h1>
</section>

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
                            <a class="btn-cv" href="<?= htmlspecialchars($app['cv_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">Voir le CV</a>
                        <?php else: ?>
                            <p class="no-cv">Aucun CV fourni</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>