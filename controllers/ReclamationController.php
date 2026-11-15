<?php
require_once __DIR__ . '/../models/ReclamationModel.php';

// 1. عرض قائمة الشكايات الخاصة بالمواطن
function afficherListe() {
    if (!isset($_SESSION['id_citoyen'])) {
        header('Location: index.php?action=login_citoyen');
        exit;
    }
    
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM reclamation WHERE id_citoyen = :id");
    $stmt->execute([':id' => $_SESSION['id_citoyen']]);
    $reclamations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    require_once 'views/afficher_reclamations.php';
}

// 2. إضافة شكاية جديدة (مع حفظ الصورة والـ GPS)
function gererAjout() {
    if (!isset($_SESSION['id_citoyen'])) {
        header('Location: index.php?action=login_citoyen');
        exit;
    }

    // إذا الإرسال صار بـ POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $type = $_POST['type_probleme'];
        $repere = $_POST['repere'];
        $latitude = $_POST['latitude'] ?? null;
        $longitude = $_POST['longitude'] ?? null;
        $id_citoyen = $_SESSION['id_citoyen'];

        // تحديد الأولوية أوتوماتيكياً
        $urgences = ["Odeurs", "Débordements d'eau usées", "Affaissement, casse du réseau", "Bouchon sur branchement et retour des eaux"];
        $priorite = in_array($type, $urgences) ? 'Urgente' : 'Normale';

        // حفظ الصورة المأخوذة بالكاميرا (Base64)
        $photo_path = null;
        if (!empty($_POST['photo_base64'])) {
            if (!is_dir('uploads/')) { mkdir('uploads/', 0777, true); }
            
            $image_parts = explode(";base64,", $_POST['photo_base64']);
            if (count($image_parts) == 2) {
                $photo_path = 'uploads/' . time() . '_' . uniqid() . '.jpg';
                file_put_contents($photo_path, base64_decode($image_parts[1]));
            }
        }

        // تسجيل البيانات في قاعدة البيانات عبر الـ Model
        ajouterReclamationBDD($id_citoyen, $type, $repere, $photo_path, $latitude, $longitude, $priorite);
        
        header('Location: index.php?action=afficher');
        exit;
    }

    // عرض واجهة الإضافة العادية
    require_once 'views/ajouter_reclamation.php';
}

// 3. تعديل شكاية (خاص بالـ Admin)
function modifierReclamationForm(int $id) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM reclamation WHERE id_reclamation = :id");
    $stmt->execute([':id' => $id]);
    $recla = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($recla) {
        require_once 'views/modifier_reclamation.php';
    } else {
        die("Réclamation introuvable!");
    }
}

function updateReclamationAction(int $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        updateReclamation($id, $_POST['type'], $_POST['repere']); 
        header("Location: index.php?action=dashboard_employer");
        exit;
    }
}

// 4. حذف شكاية (خاص بالـ Admin)
function supprimerReclamationAction(int $id) {
    supprimerReclamation($id);
    header("Location: index.php?action=dashboard_employer");
    exit;
}
?>