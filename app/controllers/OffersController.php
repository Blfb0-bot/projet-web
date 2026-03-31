<?php

class OffersController {
    public function index() {
        require_once ROOT . '/app/models/OfferModel.php';
        $offers = (new OfferModel())->getAll();

        require_once ROOT . '/app/views/pages/offers.php';
    }
    public function create() {
        require_once ROOT . '/app/models/OfferModel.php';
        // 1. Récupérer les données du formulaire
        $data = [
            'titre'        => $_POST['titre'] ?? '',
            'description'  => $_POST['description'] ?? '',
            'competences'  => $_POST['competences'] ?? '',
            'remuneration' => $_POST['remuneration'] ?? null,
            'date_fin'     => $_POST['date_fin'] ?? null,
        ];
        // 2. Valider un minimum (exemple)
        if (trim($data['titre']) === '' || trim($data['description']) === '') {
            // gérer l’erreur (message, redirection, etc.)
            header('Location: /offers?error=missing_fields');
            exit;
        }
        // 3. Appeler le modèle
        $offerModel = new OfferModel();
        $offerModel->create($data);
        // 4. Rediriger vers la liste
        header('Location: /offers');
        exit;

    }
}
?>