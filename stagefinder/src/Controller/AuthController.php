<?php
declare(strict_types=1);
namespace App\Controller;

use App\Model\UserModel;
use App\Middleware\AuthMiddleware;
use App\View\TwigRenderer;

class AuthController
{
    public function loginForm(array $p): void
    {
        if (AuthMiddleware::isLoggedIn()) { header('Location: /'); exit; }
        TwigRenderer::render('auth/login.html.twig', [
            'csrf' => AuthMiddleware::csrfGenerate(),
        ]);
    }

    public function login(array $p): void
    {
        AuthMiddleware::csrfVerify();

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
        $pass  = $_POST['mot_de_passe'] ?? '';
        $errors = [];

        if (empty($email)) $errors[] = 'Email requis.';
        if (empty($pass))  $errors[] = 'Mot de passe requis.';

        if (empty($errors)) {
            $model = new UserModel();
            $user  = $model->findByEmail($email);
            if ($user && password_verify($pass, $user['mot_de_passe'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']     = $user['id'];
                $_SESSION['user_role']   = $user['role'];
                $_SESSION['user_nom']    = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                header('Location: /'); exit;
            }
            $errors[] = 'Email ou mot de passe incorrect.';
        }

        TwigRenderer::render('auth/login.html.twig', [
            'errors' => $errors,
            'email'  => $email,
            'csrf'   => AuthMiddleware::csrfGenerate(),
        ]);
    }

    public function logout(array $p): void
    {
        session_destroy();
        header('Location: /connexion'); exit;
    }
}
