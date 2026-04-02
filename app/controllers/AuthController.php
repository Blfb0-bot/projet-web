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
                session_regenerate_id();
                
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
}