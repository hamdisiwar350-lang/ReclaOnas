<?php
require_once __DIR__ . '/../models/CitoyenModel.php';

class CitoyenController {
    private $model;

    public function __construct() {
        $this->model = new CitoyenModel();
    }

    // معالجة الـ Inscription
    public function inscriptionAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cin = trim($_POST['cin']);
            $nom_prenom = trim($_POST['nom_prenom']);
            $telephone = trim($_POST['telephone']);
            $password = $_POST['password'];

            $result = $this->model->ajouterCitoyen($cin, $nom_prenom, $telephone, $password);

            if ($result) {
                // تقيد بنجاح، نهزوه لصفحة الدخول
                header("Location: index.php?action=login_citoyen&success=1");
                exit();
            } else {
                $erreur = "Ce numéro de CIN est déjà inscrit.";
                require_once 'views/inscription_citoyen.php';
                return;
            }
        }
        require_once 'views/inscription_citoyen.php';
    }

    // معالجة الـ Login
   public function loginAction() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cin = trim($_POST['cin']);
        $password = $_POST['password'];

        // جلب المواطن من القاعدة
        $citoyen = $this->model->getCitoyenByCin($cin);

        // التثبت من وجود المواطن وصحة كلمة السر
        if ($citoyen && password_verify($password, $citoyen['password'])) {
            
            // تسجيل المعطيات الصحيحة في الـ Session
            $_SESSION['id_citoyen'] = $citoyen['id_citoyen']; 
            $_SESSION['nom_prenom'] = $citoyen['nom_prenom'];
            $_SESSION['role'] = 'citoyen';

            // التوجيه للـ Dashboard
            header("Location: index.php?action=dashboard_citoyen");
            exit();
        } else {
            $erreur = "Numéro de CIN ou mot de passe incorrect.";
            require_once 'views/login_citoyen.php';
        }
    } else {
        require_once 'views/login_citoyen.php';
    }

}

    // عرض الـ Dashboard للمواطن
    public function dashboardAction() {
        if (!isset($_SESSION['id_citoyen'])) {
            header("Location: index.php?action=login_citoyen");
            exit();
        }
        require_once 'views/dashboard_citoyen.php';
    }
}
?>
