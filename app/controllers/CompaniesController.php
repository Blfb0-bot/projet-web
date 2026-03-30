<?php
class EntrepriseController {
    public function index() {
        require_once __DIR__ . '/../models/CompanyModel.php';
        $companies = (new CompanyModel())->getAll();

        require_once 'app/views/pages/companies.php';
    }
}
?>