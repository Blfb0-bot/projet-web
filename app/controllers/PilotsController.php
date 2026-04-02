<?php
declare(strict_types=1);
class PilotsController {
    private const REDIRECT_LIST = '/index.php?controller=pilots&action=index';
    public function index(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['admin', 'pilote']);
        $cssExtra = '<link rel="stylesheet" href="/public/styles/pilote.css">';
        $pageTitle = 'Pilotes — Web for All';
        $searchTerm = $GET['search'] ?? null;
        $formBase = 'index.php?controller=pilots&action=';
        require_once ROOT . '/app/models/UserModel.php';
        $model = new UserModel();
        if ($searchTerm){
            $pilot = $model->searchByRoleAndName('pilote', $searchTerm);
        }else{
            $pilot = $model->getByRole('pilote');
        }
        $page = ROOT . '/app/views/pages/pilots.php';
        require_once ROOT . '/app/views/layout/layout.php';
    }
    public function create(): void{
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['pilote', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . self::REDIRECT_LIST);
            exit;
        }
        require_once ROOT . '/app/models/UserModel.php';

        $prenom = trim((string)($_POST['create-prenom'] ?? ''));
        $nom = trim((string)($_POST['create-nom'] ?? ''));

        if ($prenom === '' || $nom === '') {
            header('Location: ' . self::REDIRECT_LIST . '&error=missing_fields');
            exit;
        }

        $model = new UserModel();
        
        // On vérifie si il existe déjà pour éviter les doublons
        if ($model->findIdByPrenomAndNom($prenom, $nom) !== null) {
            header('Location: ' . self::REDIRECT_LIST . '&error=known_pilot');
            exit;
        }

        // ON UTILISE LA MÉTHODE CREATE AVEC TOUTES LES DONNÉES
        $model->create([
            'prenom' => $prenom,
            'nom' => $nom,
            'role' => 'pilote'
        ]);

        header('Location: ' . self::REDIRECT_LIST . '&success=created');
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
        require_once ROOT . '/app/models/UserModel.php';
        $idRaw = $_POST['id'] ?? '';
        $id = is_numeric($idRaw) ? (int)$idRaw : null;
        $prenom = trim((string)($_POST['edit-prenom'] ?? ''));
        $nom = trim((string)($_POST['edit-nom'] ?? ''));
        
        if ($id === null || $prenom === '' || $nom === '') {
            header('Location: ' . self::REDIRECT_LIST . '&error=missing_fields');
            exit;
        }
        $model = new UserModel();
        // On vérifie si il existe déjà pour éviter les doublons
        $existingId = $model->findIdByPrenomAndNom($prenom, $nom);
        if ($existingId !== null && $existingId !== $id) {
            header('Location: ' . self::REDIRECT_LIST . '&error=known_pilot');
            exit;
        }
        // ON UTILISE LA MÉTHODE UPDATE AVEC TOUTES LES DONNÉES
        $model->update($id, [
            'prenom' => $prenom,
            'nom' => $nom
        ]);

        header('Location: ' . self::REDIRECT_LIST . '&success=updated');
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
        require_once ROOT . '/app/models/UserModel.php';
        $idRaw = $_POST['id'] ?? '';
        $id = is_numeric($idRaw) ? (int)$idRaw : null;
        
        if ($id === null) {
            header('Location: ' . self::REDIRECT_LIST . '&error=missing_fields');
            exit;
        }
        $model = new UserModel();
        $model->delete($id);
        header('Location: ' . self::REDIRECT_LIST. '&success=deleted');
        exit;
    }
}
?>