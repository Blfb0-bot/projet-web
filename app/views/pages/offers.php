<?php $cssExtra = '<link rel="stylesheet" href="/public/styles/offre.css">'; ?>

<?php
ob_start();
$formBase = '/index.php?controller=offers&action=';
$companies = $companies ?? [];
?>
<!--Le contenue de la page-->
<section id="presentation-offre">
    <h1>Nos Offres</h1>
</section>
<section id="outils-offre">
    <button type="button" id="creation-offre" onclick="ouvrir('popup-creer-offre')">créer une offre</button>
    <!-- Formulaire « vide » : les cases à cocher des cartes y sont rattachées via l'attribut form -->
    <form id="form-offers-bulk" method="post" class="offers-bulk-form"></form>
    <div class="offers-bulk-toolbar">
        <button type="button" class="btn-select-all" onclick="document.querySelectorAll('input[name=\'offer_ids[]\']').forEach(function(c){ c.checked = true; });">Tout sélectionner</button>
        <button type="button" class="btn-select-none" onclick="document.querySelectorAll('input[name=\'offer_ids[]\']').forEach(function(c){ c.checked = false; });">Tout désélectionner</button>
        <button type="submit" form="form-offers-bulk" formaction="<?= htmlspecialchars($formBase . 'openEditForSelection', ENT_QUOTES, 'UTF-8') ?>">Modifier la sélection</button>
        <button type="submit" form="form-offers-bulk" formaction="<?= htmlspecialchars($formBase . 'deleteMany', ENT_QUOTES, 'UTF-8') ?>" class="btn-danger" onclick="return confirm('Supprimer les offres cochées ?');">Supprimer la sélection</button>
    </div>
    <?php if (!empty($_GET['error'])): ?>
        <?php if ($_GET['error'] === 'bulk_none'): ?>
            <p class="form-error" role="alert">Cochez au moins une offre.</p>
        <?php elseif ($_GET['error'] === 'bulk_edit_one'): ?>
            <p class="form-error" role="alert">Pour modifier, cochez une seule offre.</p>
        <?php endif; ?>
    <?php endif; ?>
</section>
<section id="nos-offres">
    <?php foreach (($offers ?? []) as $offer): ?>
        <?php $oid = (int)($offer['id'] ?? 0); ?>
        <div class="offres">
            <div class="debut-contenu-offre">
                <?php if ($oid > 0): ?>
                    <label class="offre-select-label" title="Sélectionner pour modifier ou supprimer">
                        <input type="checkbox" form="form-offers-bulk" name="offer_ids[]" value="<?= $oid ?>">
                    </label>
                <?php endif; ?>
                <div class="type" onclick="ouvrir('popup-postuler-offre')">stage</div>
                <div class="title"><h3><?= htmlspecialchars((string)($offer['titre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h3></div>
                <div><a class="évaluer" href="#">évaluer</a></div>
            </div>
            <div class="contenu-offre">
                <div class="description">
                    <h2>description</h2>
                    <article>
                        <p><?= nl2br(htmlspecialchars((string)($offer['description'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></p>
                    </article>
                </div>
                <div class="competences">
                    <h2>compétences</h2>
                    <article>
                        <p><?= htmlspecialchars((string)($offer['competences'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                    </article>
                </div>
                <div class="detail">
                    <article><p>nombre de candidat: <?= htmlspecialchars((string)($offer['nb_candidatures'] ?? '0'), ENT_QUOTES, 'UTF-8') ?></p></article>
                    <article><p>rémunération: <?= htmlspecialchars((string)($offer['remuneration'] ?? ''), ENT_QUOTES, 'UTF-8') ?> €</p></article>
                </div>
            </div>
            <div class="fin-contenu-offre">
                <p><?= htmlspecialchars((string)($offer['date_fin'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<!-- Popups offres : formulaires POST vers OffersController (create / update) -->
<div id="popups-offres-page">
    <div class="overlay" id="popup-creer-offre">
        <div class="popup">
            <h2>Création d'une offre</h2>
            <?php if (!empty($_GET['error']) && $_GET['error'] === 'missing_fields'): ?>
                <p class="form-error">Merci de remplir l'entreprise, le titre et la description.</p>
            <?php endif; ?>
            <form action="<?= htmlspecialchars($formBase . 'create', ENT_QUOTES, 'UTF-8') ?>" method="post">
                <label for="create-id-entreprise">Entreprise</label><br/>
                <select id="create-id-entreprise" name="id_entreprise" required>
                    <option value="">— Choisir —</option>
                    <?php foreach ($companies as $c): ?>
                        <option value="<?= (int)($c['id'] ?? 0) ?>"><?= htmlspecialchars((string)($c['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select><br/>
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
        </div>
    </div>

    <?php foreach (($offers ?? []) as $offer): ?>
        <?php
        $oid = (int)($offer['id'] ?? 0);
        if ($oid <= 0) {
            continue;
        }
        $rem = $offer['remuneration'];
        $remStr = $rem !== null && $rem !== '' ? htmlspecialchars((string)$rem, ENT_QUOTES, 'UTF-8') : '';
        $dd = $offer['date_debut'] ?? '';
        $df = $offer['date_fin'] ?? '';
        $idEnt = (int)($offer['id_entreprise'] ?? 0);
        ?>
        <div class="overlay" id="popup-modifier-offre-<?= $oid ?>">
            <div class="popup">
                <h2>Modifier l'offre</h2>
                <form action="<?= htmlspecialchars($formBase . 'update', ENT_QUOTES, 'UTF-8') ?>" method="post">
                    <input type="hidden" name="id" value="<?= $oid ?>">
                    <label for="edit-ent-<?= $oid ?>">Entreprise</label><br/>
                    <select id="edit-ent-<?= $oid ?>" name="id_entreprise" required>
                        <?php foreach ($companies as $c): ?>
                            <?php $cid = (int)($c['id'] ?? 0); ?>
                            <option value="<?= $cid ?>"<?= $cid === $idEnt ? ' selected' : '' ?>><?= htmlspecialchars((string)($c['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select><br/>
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
    <?php endforeach; ?>
</div>

<?php
$openEdit = isset($_GET['open_edit']) ? (int)$_GET['open_edit'] : 0;
if ($openEdit > 0):
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ouvrir === 'function') {
        ouvrir('popup-modifier-offre-<?= $openEdit ?>');
    }
});
</script>
<?php endif; ?>

<?php
$content   = ob_get_clean();
$pageTitle = "Offre — Web for All";
require_once ROOT . '/app/views/layout/layout.php';
