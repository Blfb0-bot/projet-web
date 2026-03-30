<?php
class OffreController {
    public function index() {
        require_once __DIR__ . '/../models/OfferModel.php';
        $offres = (new OffreModels())->getAll();

        // Les vues sont en PHP "classique" : elles utilisent directement les variables.
        require_once 'app/views/pages/offre.php';
    }
}
?>