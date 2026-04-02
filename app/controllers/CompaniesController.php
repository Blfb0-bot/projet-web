<?php
declare(strict_types=1);
class CompaniesController {
    private const REDIRECT_LIST = '/index.php?controller=companies&action=index';
    public function index(): void {
        $formBase = '/index.php?controller=companies&action='; 
        $cssExtra = '<link rel="stylesheet" href="/public/styles/entreprise.css">';
        $pageTitle = 'Entreprises — Web for All';
        $searchTerm = $_GET['search'] ?? null;
        require_once ROOT . '/app/models/CompanyModel.php';
        $model = new CompanyModel();
        if ($searchTerm){
            $companies = $model->searchByName($searchTerm);
        }else{
            $companies = $model->getAll();
        }
        $page = ROOT . '/app/views/pages/companies.php';
        require_once ROOT . '/app/views/layout/layout.php';
    }
    public function create(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['pilote', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=companies&action=index');
            exit;
        }
        require_once ROOT . '/app/models/CompanyModel.php';

        $nom = trim((string)($_POST['create-nom'] ?? ''));
        $description = trim((string)($_POST['create-description'] ?? ''));
        $email = trim((string)($_POST['create-email'] ?? ''));
        $telephone = trim((string)($_POST['create-telephone'] ?? ''));

        if ($nom === '' || $description === '' || $email === '' || $telephone === '') {
            header('Location: /index.php?controller=companies&action=index&error=missing_fields');
            echo '<p class="form-error">missing_fields</p>';
            exit;
        }

        $model = new CompanyModel();
        
        // On vérifie si elle existe déjà pour éviter les doublons
        if ($model->findIdByNom($nom) !== null) {
            header('Location: /index.php?controller=companies&action=index&error=known_company');
            echo '<p class="form-error">known_company</p>';
            exit;
        }

        // ON UTILISE LA MÉTHODE CREATE AVEC TOUTES LES DONNÉES
        $model->create([
            'nom' => $nom,
            'description' => $description,
            'email' => $email,
            'telephone' => $telephone
        ]);

        header('Location: /index.php?controller=companies&action=index&success=created');
        exit;
    }
    public function update(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['pilote', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }
        require_once ROOT . '/app/config/Database.php';
        require_once ROOT . '/app/models/CompanyModel.php';
        $idRaw = $_POST['id'] ?? '';
        $id = is_numeric($idRaw) ? (int)$idRaw : null;
        $nom = trim((string)($_POST['edit-nom'] ?? ''));
        $description = trim((string)($_POST['edit-description'] ?? ''));
        $email = trim((string)($_POST['edit-email'] ?? ''));
        $telephone = trim((string)($_POST['edit-telephone'] ?? ''));

        if ($id === null || $nom === '' || $description === '' || $email === '' || $telephone === '') {
            header('Location: ' . self::REDIRECT_LIST . '&error=missing_fields');
            exit;
        }
        $model = new CompanyModel();

        $model->update($id, [
            'nom' => $nom,
            'description' => $description,
            'email' => $email,
            'telephone' => $telephone
        ]);
        header('Location: ' . self::REDIRECT_LIST. '&success=updated');
        exit;
    }
    public function delete(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['pilote', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }
        require_once ROOT . '/app/config/Database.php';
        require_once ROOT . '/app/models/CompanyModel.php';
        $idRaw = $_POST['id'] ?? '';
        $id = is_numeric($idRaw) ? (int)$idRaw : null;
        if ($id === null) {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare('DELETE FROM entreprise WHERE id = :id');
        $stmt->execute([':id' => $id]);
        header('Location: ' . self::REDIRECT_LIST);
    }
    public function evaluer(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['etudiant','admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }
        require_once ROOT . '/app/config/Database.php';
        require_once ROOT . '/app/models/CompanyModel.php';
        $idEntrepriseRaw = $_POST['id_entreprise'] ?? '';
        $idEntreprise = is_numeric($idEntrepriseRaw) ? (int)$idEntrepriseRaw : null;
        $noteRaw = $_POST['note'] ?? '';
        $note = is_numeric($noteRaw) ? (int)$noteRaw : null;
        $commentaire = trim((string)($_POST['commentaire'] ?? ''));

        if ($idEntreprise === null || $note === null || $commentaire === '') {
            header('Location: ' . self::REDIRECT_LIST . '&error=missing_fields');
            exit;
        }
        $model = new CompanyModel.php();
        $model->evaluer([
            'id_entreprise' => $idEntreprise,
            'id_etudiant' => $_SESSION['user_id'],
            'note' => $note,
            'commentaire' => $commentaire
        ]);
        header('Location: ' . self::REDIRECT_LIST. '&success=evaluated');
        exit;
    }
}
?>