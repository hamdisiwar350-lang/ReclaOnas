<?php
require_once __DIR__ . '/config/security.php';

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
session_set_cookie_params([
    'httponly' => true,
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'samesite' => 'Lax'
]);
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(self), geolocation=(self)');
session_start();

require_once 'controllers/ReclamationController.php';
require_once 'controllers/CitoyenController.php';
require_once 'controllers/EmployerController.php';
require_once 'controllers/AdminController.php';
$action = $_GET['action'] ?? 'accueil';

if ($action === 'accueil') require_once 'views/accueil.php';
elseif ($action === 'inscription_citoyen') (new CitoyenController())->inscriptionAction();
elseif ($action === 'login_citoyen') (new CitoyenController())->loginAction();
elseif ($action === 'logout') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Méthode non autorisée.');
    }
    verifyCsrfToken();
    session_unset();
    session_destroy();
    header("Location: index.php?action=accueil");
    exit();
}
elseif ($action === 'login_employer') loginEmployerForm();
elseif ($action === 'auth_employer') loginEmployerAction();
elseif ($action === 'dashboard_admin' || $action === 'dashboard_employer') afficherDashboardEmployer();
elseif ($action === 'update_statut') updateStatutAction();
elseif ($action === 'affecter') affecterAction();
elseif ($action === 'marquer_terminee') marquerTermineeAction();
elseif ($action === 'stats') (new AdminController())->getStats();
elseif ($action === 'liste_citoyens') (new AdminController())->afficherListeCitoyens();
elseif ($action === 'bloquer') (new AdminController())->bloquerCitoyen(postInt('id') ?? 0);
elseif ($action === 'debloquer') (new AdminController())->debloquerCitoyen(postInt('id') ?? 0);
elseif ($action === 'gestion_equipe') (new AdminController())->afficherGestionEquipe();
elseif ($action === 'store_chef_equipe') (new AdminController())->ajouterChefEquipeAction();
elseif ($action === 'supprimer_chef_equipe') (new AdminController())->supprimerChefEquipeAction(postInt('id') ?? 0);
elseif ($action === 'afficher') afficherListe();
elseif ($action === 'ajouter') gererAjout();
elseif ($action === 'modifier') modifierReclamationForm(queryInt('id') ?? 0);
elseif ($action === 'update') updateReclamationAction(queryInt('id') ?? 0);
elseif ($action === 'supprimer') supprimerReclamationAction(postInt('id') ?? 0);
elseif ($action === 'dashboard_citoyen') {
    if (!isset($_SESSION['id_citoyen']) || ($_SESSION['role'] ?? '') !== 'citoyen') { header("Location: index.php?action=login_citoyen"); exit(); }
    require_once 'views/dashboard_citoyen.php';
} else { header("Location: index.php?action=accueil"); exit(); }
?>