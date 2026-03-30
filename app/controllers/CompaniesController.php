<?php
class CompagniesController {
    public function index() {
        require_once __DIR__ . '/../models/CompanyModel.php';
        $companies = (new CompanyModel())->getAll();
        require_once __DIR__ . '/../app/views/pages/companies.php';
    }
}
?>