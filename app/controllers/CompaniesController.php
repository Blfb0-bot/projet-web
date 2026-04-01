<?php
declare(strict_types=1);
class CompaniesController {
    private const REDIRECT_LIST = '/index.php?controller=offers&action=index';
    public function index(): void {
        $formBase = '/index.php?controller=companies&action='; 
        $cssExtra = '<link rel="stylesheet" href="/public/styles/entreprise.css">';
        $pageTitle = 'Entreprises — Web for All';
        $page = ROOT . '/app/views/pages/companies.php';
        require_once ROOT . '/app/models/CompanyModel.php';
        $companies = (new CompanyModel())->getAll();
        require_once ROOT . '/app/views/layout/layout.php';
    }
    public function create(): void{
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=companies&action=index');
            exit;
        }
        
        require_once ROOT . '/app/models/CompanyModel.php';

        $nom = trim((string)($_POST['nom'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $telephone = trim((string)($_POST['telephone'] ?? ''));

        if ($nom === '' || $description === '' || $email === '' || $telephone === '') {
            header('Location: /index.php?controller=companies&action=index&error=missing_fields');
            exit;
        }

        $model = new CompanyModel();
        
        // On vérifie si elle existe déjà pour éviter les doublons
        if ($model->findIdByNom($nom) !== null) {
            header('Location: /index.php?controller=companies&action=index&error=known_company');
            exit;
        }

        // ON UTILISE LA MÉTHODE CREATE AVEC TOUTES LES DONNÉES
        $model->create([
            'nom' => $nom,
            'description' => $description,
            'email' => $email,
            'telephone' => $telephone
        ]);

        header('Location: /index.php?controller=companies&action=index');
        exit;
    }
    public function update(): void{
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }
        require_once ROOT . '/app/config/Database.php';
        require_once ROOT . '/app/models/CompanyModel.php';
        $idRaw = $_POST['id'] ?? '';
        $id = is_numeric($idRaw) ? (int)$idRaw : null;
        $nom = trim((string)($_POST['nom'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $telephone = trim((string)($_POST['telephone'] ?? ''));

        if ($id === null || $nom === '' || $description === '' || $email === '' || $telephone === '') {
            header('Location: ' . self::REDIRECT_LIST . '&error=missing_fields');
            exit;
        }

        $pdo = Database::getPdo();
        $stmt = $pdo->prepare('UPDATE entreprise SET nom = :nom, description = :description, email = :email, telephone = :telephone WHERE id = :id');
        $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':description' => $description,
            ':email' => $email,
            ':telephone' => $telephone,
        ]);
        header('Location: ' . self::REDIRECT_LIST);
    }
    public function delete(): void{
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
}
?>