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

    public function bloquerCitoyen($id) {
        $this->model->updateCitoyenEtat((int) $id, 'bloque');
        header('Location: index.php?action=liste_citoyens');
        exit();
    }

    public function debloquerCitoyen($id) {
        $this->model->updateCitoyenEtat((int) $id, 'actif');
        header('Location: index.php?action=liste_citoyens');
        exit();
    }

    public function getStats() {
        $stats = [
            'total' => $this->model->countTotal(),
            'attente' => $this->model->countByStatut('En attente')
        ];
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

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $matricule = trim($_POST['matricule'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $specialite = trim($_POST['specialite'] ?? '');

        if ($nom === '' || $prenom === '' || $matricule === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8 || $specialite === '') {
            http_response_code(400);
            exit('Données invalides.');
        }

        $this->model->ajouterChefEquipe($matricule, "$nom $prenom", $email, $password, $specialite);
        header("Location: index.php?action=gestion_equipe&msg=" . rawurlencode("Chef d'équipe ajouté avec succès"));
        exit();
    }

    public function supprimerChefEquipeAction($id) {
        $this->model->supprimerChefEquipe((int) $id);
        header('Location: index.php?action=gestion_equipe');
        exit();
    }
}
?>