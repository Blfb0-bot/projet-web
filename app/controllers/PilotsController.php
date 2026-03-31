<?php
class PilotsController {
    public function index(): void{
        $cssExtra = '<link rel="stylesheet" href="/public/styles/pilote.css">';
        $pageTitle = 'Pilotes — Web for All';
        $page = ROOT . '/app/views/pages/pilots.php';
        require_once ROOT . '/app/models/UserModel.php';
        require_once ROOT . '/app/views/layout/layout.php';
        $pilots = (new UserModel())->getByRole('pilote');
    }
}
?>