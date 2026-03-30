<?php
class EtudiantController {
    public function index() {
        require_once __DIR__ . '/../models/UserModel.php';
        $students = (new UserModel())->getByRole('etudiant');

        require_once 'app/views/pages/etudiant.php';
    }
}
?>