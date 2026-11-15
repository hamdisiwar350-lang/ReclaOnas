<?php
session_start();

require_once 'controllers/ReclamationController.php';
require_once 'controllers/CitoyenController.php';
require_once 'controllers/EmployerController.php';
require_once 'controllers/AdminController.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'accueil';

// ==========================================
// Router
// ==========================================

if ($action === 'accueil') {
    require_once 'views/accueil.php';
}

elseif ($action === 'inscription_citoyen') {
    $citoyen = new CitoyenController();
    $citoyen->inscriptionAction();
} 
elseif ($action === 'login_citoyen') {
    $citoyen = new CitoyenController();
    $citoyen->loginAction();
} 
elseif ($action === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php?action=accueil");
    exit();
}

elseif ($action === 'login_employer') {
    loginEmployerForm();
} 
elseif ($action === 'auth_employer') {
    loginEmployerAction();
}
elseif ($action === 'dashboard_admin') {
    afficherDashboardEmployer();
}
elseif ($action === 'dashboard_employer') {
    afficherDashboardEmployer();
}
elseif ($action === 'update_statut') {
    updateStatutAction();
}
elseif ($action === 'affecter') {
    affecterAction();
}
// في وسط الـ index.php
elseif ($action === 'marquer_terminee') {
    marquerTermineeAction();
}

// --- (Admin : Statistiques & Gestion Citoyens) ---
elseif ($action === 'stats') {
    $admin = new AdminController();
    $admin->getStats();
}
elseif ($action === 'liste_citoyens') {
    $admin = new AdminController();
    $admin->afficherListeCitoyens();
}
elseif ($action === 'bloquer') {
    $admin = new AdminController();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $admin->bloquerCitoyen($id);
}

// ديبلوكاج مواطن
elseif ($action === 'debloquer') {
    $admin = new AdminController();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $admin->debloquerCitoyen($id);
}
// --- (Admin : Gestion Équipes) ---
elseif ($action === 'gestion_equipe') {
    $admin = new AdminController();
    $admin->afficherGestionEquipe();
}
elseif ($action === 'store_chef_equipe') {
    $admin = new AdminController();
    $admin->ajouterChefEquipeAction();
}
elseif ($action === 'supprimer_chef_equipe') {
    $admin = new AdminController();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $admin->supprimerChefEquipeAction($id);
}

// --- (Reclamations) ---
elseif ($action === 'afficher') {
    afficherListe();
} 
elseif ($action === 'ajouter') {
    gererAjout();
} 
elseif ($action === 'modifier') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    modifierReclamationForm($id);
} 
elseif ($action === 'update') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    updateReclamationAction($id);
} 
elseif ($action === 'supprimer') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    supprimerReclamationAction($id);
}
elseif ($action === 'dashboard_citoyen') {
    if (!isset($_SESSION['id_citoyen'])) {
        header("Location: index.php?action=login_citoyen");
        exit();
    }
    require_once 'views/dashboard_citoyen.php';
}

else {
    header("Location: index.php?action=accueil");
    exit();
}
?>
