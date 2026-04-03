<?php
declare(strict_types=1);
require_once ROOT . '/app/models/WishlistModel.php';

class WishlistController {
    private WishlistModel $model;
    public function __construct() {
        $this->model = new WishlistModel();
    }
    // Réponse JSON propre
    private function json(array $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    // Vérifie le token CSRF sur les requêtes POST
    private function verifierCsrf(): void {
        if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
            $this->json(['error' => 'Token CSRF invalide'], 403);
        }
    }
    // SF23 — Afficher la wish-list
    public function index(): void {
        verifierRole(['etudiant']);

        $offres = $this->model->getByEtudiant((int) $_SESSION['user_id']);
        require ROOT . '/app/views/wishlist/index.php';
    }
    // SF24 — Ajouter une offre à la wish-list (appelé en AJAX)
    public function ajouter(): void {
        verifierRole(['etudiant']);
        $this->verifierCsrf();

        $offre_id = intval($_POST['offre_id'] ?? 0);
        if (!$offre_id) {
            $this->json(['error' => 'Offre invalide'], 400);
        }

        if ($this->model->existe((int) $_SESSION['user_id'], $offre_id)) {
            $this->json(['error' => 'Déjà dans ta wish-list'], 409);
        }

        $ok = $this->model->ajouter((int) $_SESSION['user_id'], $offre_id);
        $ok
            ? $this->json(['success' => true, 'message' => 'Offre ajoutée à ta wish-list !'])
            : $this->json(['error' => 'Erreur serveur'], 500);
    }
    // SF25 — Retirer une offre de la wish-list (appelé en AJAX)
    public function retirer(): void {
        verifierRole(['etudiant']);
        $this->verifierCsrf();

        $offre_id = intval($_POST['offre_id'] ?? 0);
        if (!$offre_id) {
            $this->json(['error' => 'Offre invalide'], 400);
        }

        $ok = $this->model->retirer((int) $_SESSION['user_id'], $offre_id);
        $ok
            ? $this->json(['success' => true, 'message' => 'Offre retirée de ta wish-list.'])
            : $this->json(['error' => 'Erreur serveur'], 500);
    }
}