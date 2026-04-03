<?php
declare(strict_types=1);

final class ApplicationsController {
    private const REDIRECT_LIST = '/index.php?controller=applications&action=index';
    public function index(): void {
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['etudiant', 'pilote', 'admin']);

        require_once ROOT . '/app/models/ApplicationModel.php';
        require_once ROOT . '/app/models/UserModel.php'; // ajouter
        $model     = new ApplicationModel();
        $userModel = new UserModel();                    // ajouter

        $role   = $_SESSION['user_role'];
        $userId = (int)$_SESSION['user_id'];

        if ($role === 'etudiant') {
            $applications = $model->getByStudent($userId);
            $pilotes      = $userModel->getByRole('pilote'); // ajouter
        } else {
            $applications = $model->getByPilot($userId);
            $pilotes      = [];
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
    public function cv(): void {
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['etudiant', 'pilote', 'admin']);
        require_once ROOT . '/app/models/ApplicationModel.php';
        $model = new ApplicationModel();
        $id     = (int)($_GET['id'] ?? 0);
        $userId = (int)$_SESSION['user_id'];
        $role   = $_SESSION['user_role'];
        $app = $model->getById($id);
        if (!$app) {
            http_response_code(404);
            exit('Fichier introuvable.');
        }
        // Vérification d'accès : étudiant ne voit que son propre CV
        if ($role === 'etudiant' && (int)$app['id_etudiant'] !== $userId) {
            http_response_code(403);
            exit('Accès refusé.');
        }
        $filePath = ROOT . $app['cv_path'];
        if (!file_exists($filePath)) {
            http_response_code(404);
            exit('Fichier introuvable.');
        }
        $mime = mime_content_type($filePath);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
    public function assignPilot(): void {
        require_once ROOT . '/app/controllers/UserController.php';
        verifierRole(['etudiant']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /index.php?controller=applications&action=index');
            exit;
        }

        $idPilote   = (int)($_POST['id_pilote'] ?? 0);
        $idEtudiant = (int)$_SESSION['user_id'];

        if ($idPilote <= 0) {
            header('Location: /index.php?controller=applications&action=index&error=invalid_pilot');
            exit;
        }

        require_once ROOT . '/app/models/UserModel.php';
        (new UserModel())->updatePilot($idEtudiant, $idPilote);

        $_SESSION['user_pilote'] = $idPilote;

        header('Location: /index.php?controller=applications&action=index&success=pilot_assigned');
        exit;
    }
}