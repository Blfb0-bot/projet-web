<?php
declare(strict_types=1);

final class ApplicationController {

    private const REDIRECT_LIST = '/index.php?controller=applications&action=index';

    public function index(): void {
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['etudiant', 'pilote', 'admin']);

        require_once ROOT . '/app/models/ApplicationModel.php';
        $model = new ApplicationModel();

        $role   = $_SESSION['user_role'];
        $userId = (int)$_SESSION['user_id'];

        if ($role === 'etudiant') {
            $applications = $model->getByStudent($userId);
        } else {
            $applications = $model->getByPilot($userId);
        }

        $cssExtra  = '<link rel="stylesheet" href="/public/styles/application.css">';
        $pageTitle = 'Applications — Web for All';
        $page      = ROOT . '/app/views/pages/applications.php';
        require_once ROOT . '/app/views/layout/layout.php';
    }

    public function create(): void {
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['etudiant']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=offers&action=index');
            exit;
        }

        require_once ROOT . '/app/models/ApplicationModel.php';
        $model = new ApplicationModel();

        $idOffre    = (int)($_POST['id_offre'] ?? 0);
        $idEtudiant = (int)$_SESSION['user_id'];
        $lm         = trim((string)($_POST['lettre_motivation'] ?? ''));

        if ($idOffre <= 0 || $lm === '') {
            header('Location: /index.php?controller=offers&action=index&error=missing_fields');
            exit;
        }

        if ($model->alreadyApplied($idOffre, $idEtudiant)) {
            header('Location: /index.php?controller=offers&action=index&error=already_applied');
            exit;
        }

        $cvPath = null;
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $allowed = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            $mimeType = mime_content_type($_FILES['cv']['tmp_name']);

            if (!in_array($mimeType, $allowed, true)) {
                header('Location: /index.php?controller=offers&action=index&error=invalid_cv');
                exit;
            }

            if ($_FILES['cv']['size'] > 5 * 1024 * 1024) {
                header('Location: /index.php?controller=offers&action=index&error=cv_too_large');
                exit;
            }

            $uploadDir = ROOT . '/public/uploads/cvs/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext      = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
            $filename = 'cv_' . $idEtudiant . '_' . $idOffre . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['cv']['tmp_name'], $uploadDir . $filename);
            $cvPath = '/public/uploads/cvs/' . $filename;
        }

        $model->create([
            'id_offre'          => $idOffre,
            'id_etudiant'       => $idEtudiant,
            'lettre_motivation' => $lm,
            'cv_path'           => $cvPath,
        ]);

        header('Location: /index.php?controller=offers&action=index&success=applied');
        exit;
    }
}