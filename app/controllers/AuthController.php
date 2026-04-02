<?php
declare(strict_types=1);
require_once ROOT . '/app/models/UserModel.php';
final class AuthController {
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new UserModel();

            // 1. Vérifier si l'utilisateur existe déjà
            $existingUser = $userModel->getByEmail($_POST['email']);
            if ($existingUser) {
                header('Location: index.php?error=email_exists');
                exit();
            }

            // 2. Préparer les données
            $hashedPassword = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
            
            $data = [
                'prenom'       => $_POST['prenom'],
                'nom'          => $_POST['nom'],
                'email'        => $_POST['email'],
                'mot_de_passe' => $hashedPassword,
                'role'         => $_POST['role'] ?? 'visiteur' 
            ];

            // 3. Créer le compte et récupérer l'ID généré
            $newUserId = $userModel->create($data);

            if ($newUserId) {
                // 4. CONNEXION AUTOMATIQUE
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['user_prenom'] = $data['prenom'];
                $_SESSION['user_nom'] = $data['nom'];
                $_SESSION['user_role'] = $data['role'];

                // Redirection vers l'accueil (le bouton profil changera tout seul)
                header('Location: index.php?success=welcome');
                exit();
            }
        }
    }
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['mot_de_passe'] ?? '';

            $userModel = new UserModel();
            $user = $userModel->getByEmail($email);

            // On vérifie si l'utilisateur existe ET si le mot de passe est correct
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Régénérer l'ID de session pour la sécurité
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_role'] = $user['role'];

                header('Location: index.php?controller=accueil&action=index');
                exit();
            } else {
                // Identifiants incorrects
                header('Location: index.php?error=login_failed');
                exit();
            }
        }
    }
    public function logout() {
        // On vide le tableau de session
        $_SESSION = [];
        // On détruit la session
        session_destroy();
        // Redirection vers l'accueil
        header('Location: index.php?controller=accueil&action=index');
        exit();
    }
    public function profil() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'modifier') {
                $prenom = htmlspecialchars($_POST['prenom']);
                $nom    = htmlspecialchars($_POST['nom']);
                $email  = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

                if (!$email) {
                    $_SESSION['error'] = 'Email invalide.';
                } else {
                    // $this->userModel->update($_SESSION['user_id'], $prenom, $nom, $email);
                    $_SESSION['success'] = 'Profil mis à jour.';
                }

            } elseif ($action === 'password') {
                $actuel   = $_POST['password_actuel'];
                $nouveau  = $_POST['password_nouveau'];
                $confirm  = $_POST['password_confirm'];

                if ($nouveau !== $confirm) {
                    $_SESSION['error'] = 'Les mots de passe ne correspondent pas.';
                } elseif (strlen($nouveau) < 8) {
                    $_SESSION['error'] = 'Minimum 8 caractères.';
                } else {
                    // Vérifier l'ancien hash, puis mettre à jour
                    // $hash = password_hash($nouveau, PASSWORD_BCRYPT);
                    // $this->userModel->updatePassword($_SESSION['user_id'], $hash);
                    $_SESSION['success'] = 'Mot de passe modifié.';
                }

            } elseif ($action === 'supprimer') {
                $confirm = $_POST['confirm_suppression'] ?? '';
                if ($confirm === 'SUPPRIMER') {
                    // $this->userModel->delete($_SESSION['user_id']);
                    session_destroy();
                    header('Location: /');
                    exit;
                }
            }

            header('Location: /profil');
            exit;
        }

        // Affichage de la vue avec le popup
        $user = $_SESSION['user'] ?? [];
        include 'views/profil.php';
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
        }
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
    }
}