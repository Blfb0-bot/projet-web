<?php
class AccueilController {
    public function index(): void{
        $page = ROOT . '/app/views/pages/accueil.php';
        require_once ROOT . '/app/views/layout/layout.php';
    }
}
?>