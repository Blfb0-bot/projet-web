<?php
require_once ROOT . 'app/models/EntrepriseModel.php';

class EntrepriseController {
    private $model;

    public function __construct() {
        $this->model = new EntrepriseModel();
    }

    public function index() {
        $entreprises = $this->model->getAll();
        require_once ROOT . 'app/views/pages/entreprise.php';
    }
}
