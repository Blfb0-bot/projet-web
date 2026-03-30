<?php
class CompaniesController {
    public function index() {
        require_once ROOT . '/app/models/CompanyModel.php';
        $companies = (new CompanyModel())->getAll();
        require_once ROOT . '/app/views/pages/companies.php';
    }
}
?>