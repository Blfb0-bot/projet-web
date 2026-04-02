<?php
declare(strict_types=1);

final class OffersController{
    private const REDIRECT_LIST = '/index.php?controller=offers&action=index';
    public function index(): void{
        $formBase = '/index.php?controller=offers&action=';
        $cssExtra = '<link rel="stylesheet" href="/public/styles/offre.css">';
        $pageTitle = 'Offres — Web for All';
        $page = ROOT . '/app/views/pages/offers.php';
        require_once ROOT . '/app/models/OfferModel.php';
        $offers = (new OfferModel())->getAll();
        require_once ROOT . '/app/views/layout/layout.php';
    }
    public function create(): void{
    require_once ROOT . '/app/controllers/UserController.php';
    verifierRole(['pilote', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=offers&action=index' );
            exit;
        }
        require_once ROOT . '/app/models/OfferModel.php';
        require_once ROOT . '/app/models/CompanyModel.php';

        $entrepriseNom = trim((string)($_POST['create-entreprise_nom'] ?? ''));
        $titre = trim((string)($_POST['create-titre'] ?? ''));
        $description = trim((string)($_POST['create-description'] ?? ''));
        $competencesText = trim((string)($_POST['create-competences'] ?? ''));
        $remunerationRaw = $_POST['create-remuneration'] ?? '';
        $remuneration = $remunerationRaw === '' || $remunerationRaw === null
            ? null
            : (float)$remunerationRaw;
        $dateDebut = trim((string)($_POST['create-date_debut'] ?? ''));
        $dateFin = trim((string)($_POST['create-date_fin'] ?? ''));

        if ($titre === '' || $description === '' || $entrepriseNom === '') {
            header('Location: ' . self::REDIRECT_LIST . '&error=missing_fields');
            exit;
        }

        $companyModel = new CompanyModel();
        $idEntreprise = $companyModel->findIdByNom($entrepriseNom);
        if ($idEntreprise === null) {
            header('Location: /index.php?controller=offers&action=index&error=company_not_found');
            echo '<p class="form-error">company_not_found</p>';
            exit;
        }

        $model = new OfferModel();
        if($model->findIdByTitreAndCompany($titre, $idEntreprise) !== null) {
            header('Location: /index.php?controller=offers&action=index&error=unknown_company and/or offers already exists');
            echo '<p class="form-error">unknown_company and/or offers already exists</p>';
            exit;
        }

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

        header('Location: /index.php?controller=offers&action=index&success=created');
        exit;
    }
    public function update(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['pilote', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }
        require_once ROOT . '/app/models/OfferModel.php';
        require_once ROOT . '/app/models/CompanyModel.php';
        require_once ROOT . '/app/config/Database.php';

        $id = (int)($_POST['id'] ?? 0);
        $entrepriseNom = trim((string)($_POST['edit-entreprise_nom'] ?? ''));
        $titre = trim((string)($_POST['edit-titre'] ?? ''));
        $description = trim((string)($_POST['edit-description'] ?? ''));
        $competencesText = trim((string)($_POST['edit-competences'] ?? ''));
        $remunerationRaw = $_POST['edit-remuneration'] ?? '';
        $remuneration = $remunerationRaw === '' || $remunerationRaw === null
            ? null
            : (float)$remunerationRaw;
        $dateDebut = trim((string)($_POST['edit-date_debut'] ?? ''));
        $dateFin = trim((string)($_POST['edit-date_fin'] ?? ''));

        $idEntreprise = (new CompanyModel())->findIdByNom($entrepriseNom);

        if ($id <= 0 || $idEntreprise === null || $titre === '' || $description === '') {
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
    public function delete(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['pilote', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }
        require_once ROOT . '/app/models/OfferModel.php';
        require_once ROOT . '/app/config/Database.php';

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            (new OfferModel())->delete($id);
        }

        header('Location: ' . self::REDIRECT_LIST);
        exit;
    }
}
?>