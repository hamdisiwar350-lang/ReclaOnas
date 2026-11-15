<?php
require_once __DIR__ . '/config/security.php';

session_set_cookie_params([
    'httponly' => true,
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'samesite' => 'Lax'
]);
session_start();

require_once 'controllers/ReclamationController.php';
require_once 'controllers/CitoyenController.php';
require_once 'controllers/EmployerController.php';
require_once 'controllers/AdminController.php';

$action = $_GET['action'] ?? 'accueil';

if ($action === 'accueil') {
    require_once 'views/accueil.php';
} elseif ($action === 'inscription_citoyen') {
    (new CitoyenController())->inscriptionAction();
} elseif ($action === 'login_citoyen') {
    (new CitoyenController())->loginAction();
} elseif ($action === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php?action=accueil");
    exit();
} elseif ($action === 'login_employer') {
    loginEmployerForm();
} elseif ($action === 'auth_employer') {
    loginEmployerAction();
} elseif ($action === 'dashboard_admin' || $action === 'dashboard_employer') {
    afficherDashboardEmployer();
} elseif ($action === 'update_statut') {
    updateStatutAction();
} elseif ($action === 'affecter') {
    affecterAction();
} elseif ($action === 'marquer_terminee') {
    marquerTermineeAction();
} elseif ($action === 'stats') {
    (new AdminController())->getStats();
} elseif ($action === 'liste_citoyens') {
    (new AdminController())->afficherListeCitoyens();
} elseif ($action === 'bloquer') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
    (new AdminController())->bloquerCitoyen($id);
} elseif ($action === 'debloquer') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
    (new AdminController())->debloquerCitoyen($id);
} elseif ($action === 'gestion_equipe') {
    (new AdminController())->afficherGestionEquipe();
} elseif ($action === 'store_chef_equipe') {
    (new AdminController())->ajouterChefEquipeAction();
} elseif ($action === 'supprimer_chef_equipe') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
    (new AdminController())->supprimerChefEquipeAction($id);
} elseif ($action === 'afficher') {
    afficherListe();
} elseif ($action === 'ajouter') {
    gererAjout();
} elseif ($action === 'modifier') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
    modifierReclamationForm($id);
} elseif ($action === 'update') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
    updateReclamationAction($id);
} elseif ($action === 'supprimer') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
    supprimerReclamationAction($id);
} elseif ($action === 'dashboard_citoyen') {
    if (!isset($_SESSION['id_citoyen']) || ($_SESSION['role'] ?? '') !== 'citoyen') {
        header("Location: index.php?action=login_citoyen");
        exit();
    }
    require_once 'views/dashboard_citoyen.php';
} else {
    header("Location: index.php?action=accueil");
    exit();
}
?>