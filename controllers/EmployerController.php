<?php
require_once __DIR__ . '/../models/ReclamationModel.php';
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/EmployerModel.php';
require_once __DIR__ . '/../config/database.php';

function loginEmployerForm() {
    require_once 'views/login_employer.php';
}

function updateStatutAction() {
    if (!isset($_SESSION['id_employer']) || !in_array($_SESSION['role'] ?? '', ['admin', 'equipe'], true)) {
        header("Location: index.php?action=login_employer");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?action=dashboard_employer");
        exit;
    }

    $id_reclamation = filter_input(INPUT_POST, 'id_reclamation', FILTER_VALIDATE_INT);
    $nouveau_statut = trim($_POST['nouveau_statut'] ?? '');
    $statutsAutorises = ['En attente', 'En cours', 'Terminée', 'Traité'];

    if (!$id_reclamation || !in_array($nouveau_statut, $statutsAutorises, true)) {
        header("Location: index.php?action=dashboard_employer");
        exit;
    }

    if (($_SESSION['role'] ?? '') === 'equipe' && !reclamationEstAssigneeEquipe($id_reclamation, (int) $_SESSION['id_employer'])) {
        http_response_code(403);
        exit('Accès refusé.');
    }

    updateStatutReclamation($id_reclamation, $nouveau_statut);
    header("Location: index.php?action=dashboard_employer");
    exit;
}

function loginEmployerAction() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?action=login_employer");
        exit;
    }

    $matricule = trim($_POST['matricule'] ?? '');
    $password = $_POST['password'] ?? '';
    $employer = getEmployerByMatricule($matricule);

    if ($employer && password_verify($password, $employer['password']) && in_array(strtolower($employer['role']), ['admin', 'equipe'], true)) {
        session_regenerate_id(true);
        $_SESSION['id_employer'] = (int) $employer['id_employer'];
        $_SESSION['role'] = strtolower($employer['role']);
        $_SESSION['nom'] = $employer['nom'];

        header("Location: index.php?action=" . ($_SESSION['role'] === 'admin' ? 'dashboard_admin' : 'dashboard_employer'));
        exit;
    }

    $erreur = "Matricule ou mot de passe incorrect.";
    require_once 'views/login_employer.php';
}

function afficherDashboardEmployer() {
    if (!isset($_SESSION['id_employer']) || !in_array($_SESSION['role'] ?? '', ['admin', 'equipe'], true)) {
        header("Location: index.php?action=login_employer");
        exit;
    }

    if ($_SESSION['role'] === 'admin') {
        $reclamations = getAllReclamations();
        $equipes = getToutesLesChefEquipes();
        require_once 'views/dashboard_admin.php';
        return;
    }

    $reclamations = getReclamationsParChefEquipe((int) $_SESSION['id_employer']);
    require_once 'views/dashboard_equipe.php';
}

function affecterAction() {
    if (!isset($_SESSION['id_employer']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header("Location: index.php?action=login_employer");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?action=dashboard_admin");
        exit;
    }

    $id_reclamation = filter_input(INPUT_POST, 'id_reclamation', FILTER_VALIDATE_INT);
    $id_equipe = filter_input(INPUT_POST, 'id_equipe', FILTER_VALIDATE_INT);
    if (!$id_reclamation || !$id_equipe) {
        header("Location: index.php?action=dashboard_admin");
        exit;
    }

    affecterReclamationAdmin($id_reclamation, $id_equipe);
    header("Location: index.php?action=dashboard_admin");
    exit;
}

function marquerTermineeAction() {
    if (!isset($_SESSION['id_employer']) || ($_SESSION['role'] ?? '') !== 'equipe') {
        header("Location: index.php?action=login_employer");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?action=dashboard_employer");
        exit;
    }

    $id_reclamation = filter_input(INPUT_POST, 'id_reclamation', FILTER_VALIDATE_INT);
    $rapport = trim($_POST['rapport'] ?? '');
    if (!$id_reclamation || $rapport === '' || !reclamationEstAssigneeEquipe($id_reclamation, (int) $_SESSION['id_employer'])) {
        http_response_code(403);
        exit('Accès refusé.');
    }

    $photo_intervention = null;
    if (isset($_FILES['photo_intervention']) && $_FILES['photo_intervention']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['photo_intervention'];
        $allowedMimeTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $maxSize = 5 * 1024 * 1024;
        $mime = $file['error'] === UPLOAD_ERR_OK ? (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) : false;

        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > $maxSize || !isset($allowedMimeTypes[$mime])) {
            http_response_code(400);
            exit('Fichier invalide.');
        }

        $dossier_upload = __DIR__ . '/../uploads/';
        if (!is_dir($dossier_upload) && !mkdir($dossier_upload, 0755, true)) {
            http_response_code(500);
            exit('Impossible de préparer le stockage.');
        }

        $nom_fichier = bin2hex(random_bytes(16)) . '.' . $allowedMimeTypes[$mime];
        $chemin_complet = $dossier_upload . $nom_fichier;
        if (!move_uploaded_file($file['tmp_name'], $chemin_complet)) {
            http_response_code(500);
            exit('Échec du téléchargement.');
        }
        $photo_intervention = 'uploads/' . $nom_fichier;
    }

    validerIntervention($id_reclamation, $rapport, $photo_intervention);
    header("Location: index.php?action=dashboard_employer");
    exit;
}
?>