<?php

declare(strict_types=1);

final class OffersController
{
    private const REDIRECT_LIST = '/index.php?controller=offers&action=index';

    public function index(): void
    {
        require_once ROOT . '/app/models/OfferModel.php';
        require_once ROOT . '/app/models/CompanyModel.php';

        $offers = (new OfferModel())->getAll();
        $companies = (new CompanyModel())->getAll();

        require_once ROOT . '/app/views/pages/offers.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }

        require_once ROOT . '/app/models/OfferModel.php';

        $idEntreprise = (int)($_POST['id_entreprise'] ?? 0);
        $titre = trim((string)($_POST['titre'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $competencesText = trim((string)($_POST['competences'] ?? ''));
        $remunerationRaw = $_POST['remuneration'] ?? '';
        $remuneration = $remunerationRaw === '' || $remunerationRaw === null
            ? null
            : (float)$remunerationRaw;
        $dateDebut = trim((string)($_POST['date_debut'] ?? ''));
        $dateFin = trim((string)($_POST['date_fin'] ?? ''));

        if ($idEntreprise <= 0 || $titre === '' || $description === '') {
            header('Location: ' . self::REDIRECT_LIST . '&error=missing_fields');
            exit;
        }

        $model = new OfferModel();
        $offerId = $model->create([
            'id_entreprise' => $idEntreprise,
            'titre' => $titre,
            'description' => $description,
            'remuneration' => $remuneration,
            'date_debut' => $dateDebut !== '' ? $dateDebut : null,
            'date_fin' => $dateFin !== '' ? $dateFin : null,
        ]);

        if ($competencesText !== '') {
            $model->syncCompetencesForOffer($offerId, $competencesText);
        }

        header('Location: ' . self::REDIRECT_LIST);
        exit;
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }

        require_once ROOT . '/app/models/OfferModel.php';

        $id = (int)($_POST['id'] ?? 0);
        $idEntreprise = (int)($_POST['id_entreprise'] ?? 0);
        $titre = trim((string)($_POST['titre'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $competencesText = trim((string)($_POST['competences'] ?? ''));
        $remunerationRaw = $_POST['remuneration'] ?? '';
        $remuneration = $remunerationRaw === '' || $remunerationRaw === null
            ? null
            : (float)$remunerationRaw;
        $dateDebut = trim((string)($_POST['date_debut'] ?? ''));
        $dateFin = trim((string)($_POST['date_fin'] ?? ''));

        if ($id <= 0 || $idEntreprise <= 0 || $titre === '' || $description === '') {
            header('Location: ' . self::REDIRECT_LIST . '&error=invalid_update');
            exit;
        }

        $model = new OfferModel();
        $model->update($id, [
            'id_entreprise' => $idEntreprise,
            'titre' => $titre,
            'description' => $description,
            'remuneration' => $remuneration,
            'date_debut' => $dateDebut !== '' ? $dateDebut : null,
            'date_fin' => $dateFin !== '' ? $dateFin : null,
        ]);
        $model->syncCompetencesForOffer($id, $competencesText);

        header('Location: ' . self::REDIRECT_LIST);
        exit;
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }

        require_once ROOT . '/app/models/OfferModel.php';

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            (new OfferModel())->delete($id);
        }

        header('Location: ' . self::REDIRECT_LIST);
        exit;
    }

    public function deleteMany(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }

        require_once ROOT . '/app/models/OfferModel.php';

        $raw = $_POST['offer_ids'] ?? [];
        if (!is_array($raw)) {
            $raw = [];
        }
        $ids = array_values(array_unique(array_filter(array_map('intval', $raw), static fn (int $x): bool => $x > 0)));

        if ($ids === []) {
            header('Location: ' . self::REDIRECT_LIST . '&error=bulk_none');
            exit;
        }

        (new OfferModel())->deleteMany($ids);

        header('Location: ' . self::REDIRECT_LIST);
        exit;
    }

    /**
     * Une seule case cochée : redirection vers la liste avec ouverture du popup d'édition pour cet id.
     */
    public function openEditForSelection(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }

        $raw = $_POST['offer_ids'] ?? [];
        if (!is_array($raw)) {
            $raw = [];
        }
        $ids = array_values(array_unique(array_filter(array_map('intval', $raw), static fn (int $x): bool => $x > 0)));

        if ($ids === []) {
            header('Location: ' . self::REDIRECT_LIST . '&error=bulk_none');
            exit;
        }
        if (count($ids) !== 1) {
            header('Location: ' . self::REDIRECT_LIST . '&error=bulk_edit_one');
            exit;
        }

        header('Location: ' . self::REDIRECT_LIST . '&open_edit=' . $ids[0]);
        exit;
    }
}
