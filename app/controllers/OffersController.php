<?php

var_dump($offers);
die();
class OffreController {
    public function index() {
        require_once __DIR__ . '/../models/OfferModel.php';
        $offers = (new OfferModel())->getAll();

        // Les vues sont en PHP "classique" : elles utilisent directement les variables.
        require_once 'app/views/pages/offre.php';
    }
}
?>