<!--Le contenue de la page-->
<section id="presentation-offre">
    <h1>Nos Offres</h1>
</section>
<section id="statistiques-offre">
    <h2>Statistiques</h2>
    <div class="stats-carousel">
        <div class="card-track">
            <div class="card-inner" id="carouselTrack">

                <?php
                $repartition = $stats['repartition_duree'] ?? [];
                $maxBar = max(array_column($repartition, 'nb_offres') ?: [1]);
                $totalRep = array_sum(array_column($repartition, 'nb_offres'));
                ?>
                <div class="stat-card">
                    <div class="card-body">
                        <span class="card-label">Indicateur 1 / 4</span>
                        <p class="card-title">Répartition par durée de stage</p>
                        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;">
                            <?php foreach ($repartition as $row): ?>
                                <?php $pct = $maxBar > 0 ? round(($row['nb_offres'] / $maxBar) * 100) : 0; ?>
                                <div class="bar-row">
                                    <span class="bar-label"><?= (int)$row['duree_mois'] ?> mois</span>
                                    <div class="bar-bg"><div class="bar-fill" style="width:<?= $pct ?>%"></div></div>
                                    <span style="font-size:13px;color:#6b7280;"><?= (int)$row['nb_offres'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <span class="sub-num"><?= (int)$totalRep ?> offres au total</span>
                    </div>
                </div>

                <?php $wishlist = $stats['top_wishlist'] ?? []; ?>
                <div class="stat-card">
                    <div class="card-body">
                        <span class="card-label">Indicateur 2 / 4</span>
                        <p class="card-title">Top offres en wish-list</p>
                        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;">
                            <?php foreach ($wishlist as $i => $row): ?>
                                <div class="wish-row" <?= $i === count($wishlist) - 1 ? 'style="border-bottom:none"' : '' ?>>
                                    <span class="wish-rank">#<?= $i + 1 ?></span>
                                    <span class="wish-title"><?= htmlspecialchars($row['titre'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($row['entreprise'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="wish-count"><?= (int)$row['nb_wishlist'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <?php $totaux = $stats['totaux'] ?? []; ?>
                <div class="stat-card">
                    <div class="card-body" style="justify-content:center;align-items:center;text-align:center;">
                        <span class="card-label">Indicateur 3 / 4</span>
                        <p class="card-title">Offres disponibles</p>
                        <div class="big-num" style="margin:1rem 0"><?= (int)($totaux['total_offres'] ?? 0) ?></div>
                        <span class="sub-num">offres actuellement en base</span>
                        <div class="grid2" style="margin-top:1.5rem">
                            <div class="metric-mini">
                                <div class="val"><?= (int)($totaux['nouvelles_ce_mois'] ?? 0) ?></div>
                                <div class="lbl">Nouvelles ce mois</div>
                            </div>
                            <div class="metric-mini">
                                <div class="val"><?= (int)($totaux['entreprises_actives'] ?? 0) ?></div>
                                <div class="lbl">Entreprises actives</div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php $cand = $stats['candidatures'] ?? []; ?>
                <div class="stat-card">
                    <div class="card-body" style="justify-content:center;align-items:center;text-align:center;">
                        <span class="card-label">Indicateur 4 / 4</span>
                        <p class="card-title">Candidatures par offre</p>
                        <div class="big-num" style="margin:1rem 0"><?= $cand['moyenne'] ?? '0' ?></div>
                        <span class="sub-num">candidatures en moyenne par offre</span>
                        <div class="grid2" style="margin-top:1.5rem">
                            <div class="metric-mini">
                                <div class="val"><?= (int)($cand['total_candidatures'] ?? 0) ?></div>
                                <div class="lbl">Total candidatures</div>
                            </div>
                            <div class="metric-mini">
                                <div class="val"><?= (int)($cand['max_sur_une_offre'] ?? 0) ?></div>
                                <div class="lbl">Maximum sur 1 offre</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="carousel-nav">
            <button class="carousel-btn" id="prevBtn" disabled>&#8592; Préc.</button>
            <div class="carousel-dots" id="carouselDots"></div>
            <button class="carousel-btn" id="nextBtn">Suiv. &#8594;</button>
        </div>
    </div>
</section>
<section id="outils-offre">
    <h2>Les offres de stage</h2>
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pilote'|| $_SESSION['user_role'] === 'admin'): ?>
        <button id="creation-offre" onclick="ouvrir('popup-creer-offre')">créer une offre</button>
    <?php endif; ?>
</section>
<div class="overlay" id="popup-creer-offre">
    <div class="popup">
        <h2>Création d'une offre</h2>
        <?php if (!empty($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'missing_fields'): ?>
                <p class="form-error">Merci de renseigner tout les champs</p>
            <?php elseif ($_GET['error'] === 'unknown_company'): ?>
                <p class="form-error">Entreprise inconnue, veuillez la créer.</p>
            <?php endif; ?>
        <?php endif; ?>
        <form action="<?= htmlspecialchars($formBase . 'create', ENT_QUOTES, 'UTF-8') ?>" method="post">
            <label for="create-entreprise">Nom de l'entreprise</label><br/>
            <input type="text" id="create-entreprise" name="create-entreprise_nom" required maxlength="200" placeholder="Ex. SoftCorp"><br/>
            <label for="create-titre">Titre de l'offre</label><br/>
            <input type="text" id="create-titre" name="create-titre" required><br/>
            <label for="create-description">Description</label><br/>
            <textarea id="create-description" name="create-description" rows="4" required></textarea><br/>
            <label for="create-competences">Compétences (séparées par des virgules)</label><br/>
            <textarea id="create-competences" name="create-competences" rows="3" placeholder="PHP, MySQL, …"></textarea><br/>
            <label for="create-remuneration">Rémunération (€)</label><br/>
            <input type="number" id="create-remuneration" name="create-remuneration" step="0.01" min="0"><br/>
            <label for="create-date-debut">Date de début</label><br/>
            <input type="date" id="create-date-debut" name="create-date-debut"><br/>
            <label for="create-date-fin">Date de fin</label><br/>
            <input type="date" id="create-date-fin" name="create-date-fin"><br/><br/>
            <input type="submit" value="Enregistrer">
            <input type="reset" value="Réinitialiser">
        </form>
        <button type="button" onclick="fermer('popup-creer-offre')">Fermer</button>
    </div>
</div>
<section id="nos-offres">
    <?php foreach (($offers ?? []) as $offer): ?>
        <?php
            $oid = (int)($offer['id'] ?? 0);
            $entrepriseNom = (string)($offer['entreprise_nom'] ?? '');
            $remStr = $offer['remuneration'] !== null && $offer['remuneration'] !== ''
                ? htmlspecialchars((string)$offer['remuneration'], ENT_QUOTES, 'UTF-8')
                : '';
            $dd = $offer['date_debut'] ?? '';
            $df = $offer['date_fin'] ?? '';
        ?>
        <div class="offres">
            <div class="debut-contenu-offre">
                <div class="type" onclick="ouvrir('popup-postuler-offre')">offre de stage</div>
                <div class="title"><h3><?= htmlspecialchars((string)($offer['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h3></div>
                <div><a class="wish-list" href="#">aimer</a></div>
                <?php if ($oid > 0): ?>
                <div class="modification-offre">
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pilote'|| $_SESSION['user_role'] === 'admin'): ?>
                        <button type="button" onclick="ouvrir('popup-modifier-offre-<?= $oid ?>')">modifier</button>
                        <button type="button" onclick="ouvrir('popup-supprimer-offre-<?= $oid ?>')">supprimer</button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="contenu-offre">
                <div class="description">
                    <h2>description</h2>
                    <article><p><?= nl2br(htmlspecialchars((string)($offer['description'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></p></article>
                </div>
                <div class="competences">
                    <h2>compétences</h2>
                    <article><p><?= htmlspecialchars((string)($offer['competences'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p></article>
                </div>
                <div class="detail">
                    <article><p>entreprise: <?= htmlspecialchars($entrepriseNom, ENT_QUOTES, 'UTF-8') ?></p></article>
                    <article><p>nombre de candidat: <?= htmlspecialchars((string)($offer['nb_candidatures'] ?? '0'), ENT_QUOTES, 'UTF-8') ?></p></article>
                    <article><p>rémunération: <?= htmlspecialchars((string)($offer['remuneration'] ?? ''), ENT_QUOTES, 'UTF-8') ?> €</p></article>
                </div>
            </div>
            <div class="fin-contenu-offre">
                <p><?= htmlspecialchars((string)($offer['date_fin'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
        <?php if ($oid > 0): ?> <!--si l'ID de l'offre est valide -->
        <div class="overlay" id="popup-modifier-offre-<?= $oid ?>">
            <div class="popup">
                <h2>Modifier l'offre</h2>
                <form action="<?= htmlspecialchars($formBase . 'update', ENT_QUOTES, 'UTF-8') ?>" method="post">
                    <input type="hidden" name="id" value="<?= $oid ?>">
                    <label for="edit-entreprise-<?= $oid ?>">Nom de l'entreprise</label><br/>
                    <input type="text" id="edit-entreprise-<?= $oid ?>" name="edit-entreprise_nom" required maxlength="200" value="<?= htmlspecialchars($entrepriseNom, ENT_QUOTES, 'UTF-8') ?>"><br/>
                    <label for="edit-titre-<?= $oid ?>">Titre</label><br/>
                    <input type="text" id="edit-titre-<?= $oid ?>" name="edit-titre" required value="<?= htmlspecialchars((string)($offer['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><br/>
                    <label for="edit-desc-<?= $oid ?>">Description</label><br/>
                    <textarea id="edit-desc-<?= $oid ?>" name="edit-description" rows="4" required><?= htmlspecialchars((string)($offer['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea><br/>
                    <label for="edit-comp-<?= $oid ?>">Compétences (virgules)</label><br/>
                    <textarea id="edit-comp-<?= $oid ?>" name="edit-competences" rows="3"><?= htmlspecialchars((string)($offer['competences'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea><br/>
                    <label for="edit-rem-<?= $oid ?>">Rémunération (€)</label><br/>
                    <input type="number" id="edit-rem-<?= $oid ?>" name="edit-remuneration" step="0.01" min="0" value="<?= $remStr ?>"><br/>
                    <label for="edit-dd-<?= $oid ?>">Date de début</label><br/>
                    <input type="date" id="edit-dd-<?= $oid ?>" name="edit-date_debut" value="<?= htmlspecialchars((string)$dd, ENT_QUOTES, 'UTF-8') ?>"><br/>
                    <label for="edit-df-<?= $oid ?>">Date de fin</label><br/>
                    <input type="date" id="edit-df-<?= $oid ?>" name="edit-date_fin" value="<?= htmlspecialchars((string)$df, ENT_QUOTES, 'UTF-8') ?>"><br/><br/>
                    <input type="submit" value="Enregistrer les modifications">
                </form>
                <button type="button" onclick="fermer('popup-modifier-offre-<?= $oid ?>')">Fermer</button>
            </div>
        </div>
        <div class="overlay" id="popup-supprimer-offre-<?= $oid ?>">
            <div class="popup">
                <h2>Supprimer cette offre ?</h2>
                <p><?= htmlspecialchars((string)($offer['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                <form action="<?= htmlspecialchars($formBase . 'delete', ENT_QUOTES, 'UTF-8') ?>" method="post">
                    <input type="hidden" name="id" value="<?= $oid ?>">
                    <button type="submit">Oui, supprimer</button>
                </form>
                <button type="button" onclick="fermer('popup-supprimer-offre-<?= $oid ?>')">Fermer</button>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
</section>