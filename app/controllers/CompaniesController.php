<?php
class CompaniesController {
    public function index(): void{
        $cssExtra = '<link rel="stylesheet" href="/public/styles/entreprise.css">';
        $pageTitle = 'Entreprises — Web for All';
        $page = ROOT . '/app/views/pages/companies.php';
        require_once ROOT . '/app/views/layout/layout.php';
    }
}
?>