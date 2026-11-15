<?php
require_once __DIR__ . '/../models/AdminModel.php';

class AdminController {
    private $model;

    public function __construct() {
        $this->model = new AdminModel();
        $this->requireAdmin();
    }

    private function requireAdmin(): void {
        if (!isset($_SESSION['id_employer']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?action=login_employer');
            exit();
        }
    }

    private function requirePost(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Méthode non autorisée.');
        }
        verifyCsrfToken();
    }

    public function bloquerCitoyen($id) {
        $this->requirePost();
        $this->model->updateCitoyenEtat((int) $id, 'bloque');
        header('Location: index.php?action=liste_citoyens');
        exit();
    }

    public function debloquerCitoyen($id) {
        $this->requirePost();
        $this->model->updateCitoyenEtat((int) $id, 'actif');
        header('Location: index.php?action=liste_citoyens');
        exit();
    }

    public function getStats() {
        $stats = ['total' => $this->model->countTotal(), 'attente' => $this->model->countByStatut('En attente')];
        $reclamations = getAllReclamations();
        $equipes = $this->model->getAllChefsEquipe();
        require_once 'views/dashboard_admin.php';
    }

    public function showList() {
        $citoyens = $this->model->getAllCitoyens();
        require_once 'views/gestion_utilisateurs.php';
    }

    public function afficherListeCitoyens() {
        $citoyens = $this->model->getAllCitoyens();
        require_once 'views/gestion_utilisateurs.php';
    }

    public function afficherGestionEquipe() {
        $equipes = $this->model->getAllChefsEquipe();
        require_once 'views/gestion_equipe.php';
    }

    public function ajouterChefEquipeAction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=gestion_equipe');
            exit();
        }
        verifyCsrfToken();

        $nom = trim(postString('nom') ?? '');
        $prenom = trim(postString('prenom') ?? '');
        $matricule = trim(postString('matricule') ?? '');
        $email = trim(postString('email') ?? '');
        $password = postString('password') ?? '';
        $specialite = trim(postString('specialite') ?? '');
        if ($nom === '' || strlen($nom) > 100 || $prenom === '' || strlen($prenom) > 100 || $matricule === '' || strlen($matricule) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254 || strlen($password) < 8 || $specialite === '' || strlen($specialite) > 150) {
            http_response_code(400);
            exit('Données invalides.');
        }

        if (!$this->model->ajouterChefEquipe($matricule, "$nom $prenom", $email, $password, $specialite)) {
            http_response_code(409);
            exit('Impossible de créer cette équipe.');
        }
        header("Location: index.php?action=gestion_equipe&msg=" . rawurlencode("Chef d'équipe ajouté avec succès"));
        exit();
    }

    public function supprimerChefEquipeAction($id) {
        $this->requirePost();
        $this->model->supprimerChefEquipe((int) $id);
        header('Location: index.php?action=gestion_equipe');
        exit();
    }
}
?>