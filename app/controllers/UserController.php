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
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new UserModel();

            try {
                $userModel->delete($_SESSION['user_id']);
                // Après suppression, on déconnecte l'utilisateur
                session_destroy();
                header('Location: index.php?success=compte_supprime');
            } catch (Exception $e) {
                header('Location: index.php?error=delete_failed');
            }
        }
    }
    public function update(){
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
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new UserModel();

            $currentPassword = $_POST['password_actuel'] ?? '';
            $newPassword = $_POST['password_nouveau'] ?? '';

            // Vérifier le mot de passe actuel
            if ($userModel->verifyPassword($_SESSION['user_id'], $currentPassword)) {
                // Mettre à jour avec le nouveau mot de passe
                try {
                    $userModel->updatePassword($_SESSION['user_id'], $newPassword);
                    header('Location: index.php?success=password_updated');
                } catch (Exception $e) {
                    header('Location: index.php?error=password_update_failed');
                }
            } else {
                header('Location: index.php?error=incorrect_current_password');
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