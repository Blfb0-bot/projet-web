<?php
class StudentsController {
    public function index() {
        require_once ROOT . '/app/models/UserModel.php';
        $students = (new UserModel())->getByRole('etudiant');

        require_once ROOT . '/app/views/pages/students.php';
    }
}
?>