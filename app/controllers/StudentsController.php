<?php
declare(strict_types=1);
class StudentsController {
    private const REDIRECT_LIST = '/index.php?controller=students&action=index';
    public function index(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['admin','etudiant', 'pilote']);
        $cssExtra = '<link rel="stylesheet" href="/public/styles/etudiant.css">';
        $pageTitle = 'Etudiants — Web for All';
        $searchTerm = $GET['search'] ?? null;
        $formBase = 'index.php?controller=students&action=';
        require_once ROOT . '/app/models/UserModel.php';
        $model = new UserModel();
        if ($searchTerm){
            $student = $model->searchByRoleAndName('etudiant', $searchTerm);
        }else{
            $student = $model->getByRole('etudiant');
        }
        $page = ROOT . '/app/views/pages/students.php';
        require_once ROOT . '/app/views/layout/layout.php';
    }
    public function create(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['etudiant', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }
        require_once ROOT . '/app/models/UserModel.php';

        $prenom = trim((string)($_POST['create-prenom'] ?? ''));
        $nom = trim((string)($_POST['create-nom'] ?? ''));
        $email = trim((string)($_POST['create-email'] ?? ''));

        if ($prenom === '' || $nom === '' || $email === '') {
            header('Location: ' . self::REDIRECT_LIST . '&error=missing_fields');
            exit;
        }

        $model = new UserModel();
        
        // On vérifie si il existe déjà pour éviter les doublons
        if ($model->findIdByPrenomAndNom($prenom, $nom) !== null) {
            header('Location: ' . self::REDIRECT_LIST . '&error=known_student');
            exit;
        }

        // ON UTILISE LA MÉTHODE CREATE AVEC TOUTES LES DONNÉES
        $model->create([
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'role' => 'etudiant'
        ]);

        header('Location: ' . self::REDIRECT_LIST . '&success=created');
        exit;
    }
    public function update(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['etudiant', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }
        require_once ROOT . '/app/config/Database.php';
        require_once ROOT . '/app/models/UserModel.php';
        $idRaw = $_POST['id'] ?? '';
        $id = is_numeric($idRaw) ? (int)$idRaw : null;
        $prenom = trim((string)($_POST['edit-prenom'] ?? ''));
        $nom = trim((string)($_POST['edit-nom'] ?? ''));
        $email = trim((string)($_POST['edit-email'] ?? ''));
        
        if ($id === null || $prenom === '' || $nom === '' || $email === '') {
            header('Location: ' . self::REDIRECT_LIST . '&error=missing_fields');
            exit;
        }

        // ON UTILISE LA MÉTHODE UPDATE AVEC TOUTES LES DONNÉES
        (new UserModel())->update($id, [
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'role' => 'etudiant'
        ]);

        header('Location: ' . self::REDIRECT_LIST . '&success=updated');
        exit;
    }
    public function delete(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['etudiant', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }
        require_once ROOT . '/app/config/Database.php';
        require_once ROOT . '/app/models/UserModel.php';
        $idRaw = $_POST['id'] ?? '';
        $id = is_numeric($idRaw) ? (int)$idRaw : null;
        
        if ($id === null) {
            header('Location: ' . self::REDIRECT_LIST . '&error=missing_fields');
            exit;
        }

        // ON UTILISE LA MÉTHODE DELETE
        (new UserModel())->delete($id);

        header('Location: ' . self::REDIRECT_LIST . '&success=deleted');
        exit;
    }
}
?>