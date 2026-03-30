<?php
class StudentsController {
    public function index() {
        require_once __DIR__ . '/../models/UserModel.php';
        $students = (new UserModel())->getByRole('etudiant');

        require_once __DIR__ . '../app/views/pages/students.php';
    }
}
?>