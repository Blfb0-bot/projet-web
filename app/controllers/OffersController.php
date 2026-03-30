<?php


class OffreController {
    public function index() {
        require_once __DIR__ . '/../models/OfferModel.php';
        $offers = (new OfferModel())->getAll();

        var_dump($offers);
        die();
        // Les vues sont en PHP "classique" : elles utilisent directement les variables.
        require_once 'app/views/pages/offers.php';
    }
}
?>