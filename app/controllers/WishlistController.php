<?php
declare(strict_types=1);

require_once ROOT . '/app/models/WishlistModel.php';

class WishlistController {
    private WishlistModel $model;
    public function __construct() {
        $this->model = new WishlistModel();
        // SF23 — Afficher la wish-list
    }
    public function index(): void {
        verifierRole(['etudiant']);
        $offres = $this->model->getByEtudiant((int) $_SESSION['user_id']);
        // Message de succès ou d'erreur après une action
        $message = null;
        if (isset($_GET['success'])) {
            $message = match($_GET['success']) {
                'ajoute'  => '✅ Offre ajoutée à ta wish-list !',
                'retire'  => '✅ Offre retirée de ta wish-list.',
                default   => null
            };
        }
        if (isset($_GET['error'])) {
            $message = match($_GET['error']) {
                'deja_present' => '⚠️ Cette offre est déjà dans ta wish-list.',
                'invalide'     => '⚠️ Offre invalide.',
                'csrf'         => '⚠️ Token de sécurité invalide.',
                default        => '⚠️ Une erreur est survenue.'
            };
        }
        require ROOT . '/app/views/wishlist/index.php';
    }
    // SF24 — Ajouter une offre à la wish-list
    public function ajouter(): void {
        verifierRole(['etudiant']);

        // Vérification CSRF
        if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
            header('Location: index.php?controller=wishlist&action=index&error=csrf');
            exit;
        }

        $offre_id = intval($_POST['offre_id'] ?? 0);
        // Vérification que l'offre_id est valide
        if (!$offre_id) {
            header('Location: index.php?controller=wishlist&action=index&error=invalide');
            exit;
        }

        // Vérification doublon
        if ($this->model->existe((int) $_SESSION['user_id'], $offre_id)) {
            header('Location: index.php?controller=wishlist&action=index&error=deja_present');
            exit;
        }

        $ok = $this->model->ajouter((int) $_SESSION['user_id'], $offre_id);

        if ($ok) {
            header('Location: index.php?controller=wishlist&action=index&success=ajoute');
        } else {
            header('Location: index.php?controller=wishlist&action=index&error=serveur');
        }
        exit;
    }
    // SF25 — Retirer une offre de la wish-list
    public function retirer(): void {
        verifierRole(['etudiant']);
        // Vérification CSRF
        if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
            header('Location: index.php?controller=wishlist&action=index&error=csrf');
            exit;
        }
        $offre_id = intval($_POST['offre_id'] ?? 0);
        if (!$offre_id) {
            header('Location: index.php?controller=wishlist&action=index&error=invalide');
            exit;
        }
        $ok = $this->model->retirer((int) $_SESSION['user_id'], $offre_id);
        if ($ok) {
            header('Location: index.php?controller=wishlist&action=index&success=retire');
        } else {
            header('Location: index.php?controller=wishlist&action=index&error=serveur');
        }
        exit;
    }
}