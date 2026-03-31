<?php
class StudentsController {
    public function index(): void{
        $cssExtra = '<link rel="stylesheet" href="/public/styles/etudiant.css">';
        $pageTitle = 'Etudiants — Web for All';
        $page = ROOT . '/app/views/pages/students.php';
        require_once ROOT . '/app/models/UserModel.php';
        $students = (new UserModel())->getByRole('etudiant');
        require_once ROOT . '/app/views/layout/layout.php';
    }
}
?>