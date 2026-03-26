<?php
require_once ROOT . 'app/models/OffreModel.php';

class OffreController {
    private $model;

    public function __construct() {
        $this->model = new OffreModel();
    }

    public function index() {
        $offres = $this->model->getAll();
        require_once ROOT . 'app/views/pages/offre.php';
    }
}