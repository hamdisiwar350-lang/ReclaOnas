<?php
// استدعاء الموديل مباشرة، بدون تعقيدات
require_once __DIR__ . '/../models/AdminModel.php';

class AdminController {

    private $model;

    public function __construct() {
        $this->model = new AdminModel();
    }

    // 1. الفونكسيون باش نبلوكيو مواطن
   // بلوكاج مواطن
    public function bloquerCitoyen($id) {
        $this->model->updateCitoyenEtat($id, 'bloque');
        header('Location: index.php?action=liste_citoyens');
        exit();
    }

    // ديبلوكاج مواطن
    public function debloquerCitoyen($id) {
        $this->model->updateCitoyenEtat($id, 'actif');
        header('Location: index.php?action=liste_citoyens');
        exit();
    }

    // 2. الفونكسيون باش نجيبو الإحصائيات
    public function getStats() {
        $stats = [
            'total' => $this->model->countTotal(),
            'attente' => $this->model->countByStatut('En attente')
        ];
        require_once 'views/dashboard_admin.php';
    }
    public function showList() {
    // نعيطو للموديل يجيب ليستة المواطنين
        $citoyens = $this->model->getAllCitoyens(); 
        require_once 'views/gestion_utilisateurs.php';
    }
    // زيدي الفونكسيون هذي داخل class AdminController في ملف controllers/AdminController.php
     function afficherListeCitoyens() {
           $citoyens = $this->model->getAllCitoyens();
           require_once 'views/gestion_utilisateurs.php';
    }
    // عرض صفحة إدارة رؤساء الفرق مع القائمة
    

    public function afficherGestionEquipe() {
        // 1. نتأكدو اللي هو مكنكتي وعندو الحق (Admin)
        if (!isset($_SESSION['id_employer']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php"); 
            exit();
        }

        // 2. نجيبو ليستة الفرق ونحطوها في $equipes باش تطابق الـ View
        $equipes = $this->model->getAllChefsEquipe();
        
        // 3. نعديوهم للـ View
        require_once 'views/gestion_equipe.php';
    }    function ajouterChefEquipeAction() {
    // 1. نلمو المعلومات اللي جاتنا مالفورميلار
    // دمجنا الإسم واللقب في متغير واحد خاطر في القاعدة عندنا كان عمود 'nom'
    $nom_complet = $_POST['nom'] . ' ' . $_POST['prenom']; 
    
    $matricule = $_POST['matricule'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $specialite = $_POST['specialite'];
    
    // 2. نبعثو البيانات للـ Model باش يصبهم في قاعدة البيانات
    $this->model->ajouterChefEquipe($matricule, $nom_complet, $email, $password, $specialite);
    
    // 3. نرجعو لصفحة إدارة الفرق ونعديو ميساج نجاح
    header("Location: index.php?action=gestion_equipe&msg=Chef d'équipe ajouté avec succès");
    exit();
    
    }

    // معالجة حذف رئيس فريق
    public function supprimerChefEquipeAction($id) {
        // نحي الـ echo وخلّي الخدمة تمشي ديريكت
        $this->model->supprimerChefEquipe($id);
        
        // إعادة التوجيه لصفحة إدارة الفرق
        header('Location: index.php?action=gestion_equipe');
        exit();
    }
}
?>