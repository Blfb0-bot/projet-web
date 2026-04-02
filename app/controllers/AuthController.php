<?php
declare(strict_types=1);
require_once ROOT . '/app/models/UserModel.php';
final class AuthController {
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new UserModel();

            // Hachage du mot de passe pour la sécurité
            $hashedPassword = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);

            $data = [
                'prenom'       => $_POST['prenom'],
                'nom'          => $_POST['nom'],
                'email'        => $_POST['email'],
                'mot_de_passe' => $hashedPassword,
                'role'         => 'etudiant' // Par défaut lors d'une inscription publique
            ];

            try {
                $userModel->create($data);
                header('Location: index.php?success=compte_cree');
            } catch (Exception $e) {
                header('Location: index.php?error=email_deja_pris');
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