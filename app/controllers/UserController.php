<?php
declare(strict_types=1);
class UserController {
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new UserModel();

            $data = [
                'prenom' => $_POST['edit-prenom'],
                'nom' => $_POST['edit-nom'],
                'email' => $_POST['edit-email']
            ];

            try {
                $userModel->update($_SESSION['user_id'], $data);
                header('Location: index.php?success=compte_mis_a_jour');
            } catch (Exception $e) {
                header('Location: index.php?error=update_failed');
            }
        }
    }
}
function verifierRole(array $rolesAutorises) {
    // 1. Si pas de session ou pas de rôle, on dégage
    if (!isset($_SESSION['user_role'])) {
        header('Location: index.php?controller=auth&action=login&error=auth_required');
        exit();
    }

    // 2. Si le rôle de l'utilisateur n'est PAS dans la liste des rôles autorisés
    if (!in_array($_SESSION['user_role'], $rolesAutorises)) {
        header('Location: index.php?error=access_denied');
        exit();
    }
}