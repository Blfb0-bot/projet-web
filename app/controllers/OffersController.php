<?php

class OffersController {
    public function index() {
        require_once ROOT . '/app/models/OfferModel.php';
        $offers = (new OfferModel())->getAll();

        require_once ROOT . '/app/views/pages/offers.php';
    }
}
?>