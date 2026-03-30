<?php
class PilotsController {
    public function index() {
        require_once ROOT . '/app/models/UserModel.php';
        $pilots = (new UserModel())->getByRole('pilote');

        require_once ROOT . '/app/views/pages/pilots.php';
    }
}
?>