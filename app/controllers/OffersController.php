<?php

class OffersController {
    public function index() {
        require_once ROOT . '/app/models/OfferModel.php';
        $offers = (new OfferModel())->getAll();

        var_dump($offers);
        die();
        // Les vues sont en PHP "classique" : elles utilisent directement les variables.
        require_once ROOT . '/app/views/pages/offers.php';
    }
}
?>