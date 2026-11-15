<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../models/ReclamationModel.php'; 
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/EmployerModel.php';
require_once __DIR__ . '/../config/database.php';

function loginEmployerForm() {
    require_once 'views/login_employer.php';
}

function updateStatutAction() {
    if (!isset($_SESSION['id_employer'])) {
        header("Location: index.php?action=login_employer");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_reclamation = (int) $_POST['id_reclamation'];
        $nouveau_statut = $_POST['nouveau_statut']; 
        
        updateStatutReclamation($id_reclamation, $nouveau_statut);
        
        header("Location: index.php?action=dashboard_employer");
        exit;
    }
}

function loginEmployerAction() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $matricule = trim($_POST['matricule']);
        $password = $_POST['password'];

        $employer = getEmployerByMatricule($matricule);

        if ($employer && password_verify($password, $employer['password'])) {
            // تخزين بيانات المستخدم في الـ Session
            $_SESSION['id_employer'] = $employer['id_employer']; 
            $_SESSION['role'] = strtolower($employer['role']); 
            $_SESSION['nom'] = $employer['nom'];

            // التوجيه الذكي حسب الـ Role بالظبط
            if ($_SESSION['role'] === 'admin') {
                header("Location: index.php?action=dashboard_admin");
            } else {
                header("Location: index.php?action=dashboard_employer"); // هذي هي البلاصة المخصصة للفريق!
            }
            exit();
        } else {
            $erreur = "Matricule ou mot de passe incorrect.";
            require_once 'views/login_employer.php';
        }
    } else {
        header("Location: index.php?action=login_employer");
        exit();
    }
}
function afficherDashboardEmployer() {
    
    if (!isset($_SESSION['id_employer'])) {
        header("Location: index.php?action=login_employer");
        exit;
    }

    if ($_SESSION['role'] === 'admin') {
        $reclamations = getAllReclamations(); 
        $equipes = getToutesLesChefEquipes(); 
        require_once 'views/dashboard_admin.php'; 
    } 
    elseif ($_SESSION['role'] === 'equipe') { 
    $id_equipe = $_SESSION['id_employer'];
    
    // 🔍 أضف السطور هذه للتأكد:
    
    $reclamations = getReclamationsParChefEquipe($id_equipe);
    

    require_once 'views/dashboard_equipe.php'; 
}
    
    else {
        die("Role introuvable : " . htmlspecialchars($_SESSION['role'])); 
    }
}

function affecterAction() {
    
    if (!isset($_SESSION['id_employer']) || $_SESSION['role'] !== 'admin') {
        header("Location: index.php?action=login_employer");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_reclamation = $_POST['id_reclamation'];
        $id_equipe = $_POST['id_equipe']; 
        
        affecterReclamationAdmin($id_reclamation, $id_equipe);
        
        header("Location: index.php?action=dashboard_admin");
        exit;
    }
}

// 🆕 دالة جديدة مستقلة خاصة بإنهاء الشكاية للـ Chef d'équipe
function marquerTermineeAction() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_reclamation = $_POST['id_reclamation'] ?? null;
        $rapport = $_POST['rapport'] ?? '';
        $photo_intervention = null;

        // معالجة رفع صورة التدخل
        if (isset($_FILES['photo_intervention']) && $_FILES['photo_intervention']['error'] === UPLOAD_ERR_OK) {
            $dossier_upload = 'uploads/'; // تأكد أن هذا المجلد موجود
            if (!is_dir($dossier_upload)) {
                mkdir($dossier_upload, 0777, true);
            }
            $nom_fichier = time() . '_' . basename($_FILES['photo_intervention']['name']);
            $chemin_complet = $dossier_upload . $nom_fichier;
            
            if (move_uploaded_file($_FILES['photo_intervention']['tmp_name'], $chemin_complet)) {
                $photo_intervention = $chemin_complet;
            }
        }

        if ($id_reclamation) {
            require_once 'models/EmployerModel.php'; 
            // استدعاء الدالة لتحديث الحالة، التقرير، والصورة
            validerIntervention($id_reclamation, $rapport, $photo_intervention);
        }

        header("Location: index.php?action=dashboard_employer");
        exit();
    }
}
?>

