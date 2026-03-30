<?php
class PiloteController {
    public function index() {
        require_once __DIR__ . '/../models/UserModel.php';
        $pilots = (new UserModel())->getByRole('pilote');

        require_once 'app/views/pages/pilots.php';
    }
}
?>