<?php
class PilotsController {
    public function index() {
        require_once __DIR__ . '/../models/UserModel.php';
        $pilots = (new UserModel())->getByRole('pilote');

        require_once __DIR__ . '../app/views/pages/pilots.php';
    }
}
?>