<?php

class OffersController {
    public function index() {
        require_once ROOT . '/../models/OfferModel.php';
        $offers = (new OfferModel())->getAll();

        var_dump($offers);
        die();
        // Les vues sont en PHP "classique" : elles utilisent directement les variables.
        require_once ROOT . '/../views/pages/offers.php';
    }
}
?>