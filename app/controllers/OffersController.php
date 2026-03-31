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
}
