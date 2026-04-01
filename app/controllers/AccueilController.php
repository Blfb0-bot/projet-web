<?php
class AccueilController {
    public function index(): void{
        $cssExtra = '<link rel="stylesheet" href="/public/styles/acceuil.css">';
        $pageTitle = 'Accueil — Web for All';
        $page = ROOT . '/app/views/pages/accueil.php';
        require_once ROOT . '/app/views/layout/layout.php';
    }
}
?>