<?php
require_once __DIR__ . '/../models/CitoyenModel.php';

class CitoyenController {
    private $model;

    public function __construct() {
        $this->model = new CitoyenModel();
    }

    public function inscriptionAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verifyCsrfToken();
            $cin = trim(postString('cin') ?? '');
            $nom_prenom = trim(postString('nom_prenom') ?? '');
            $telephone = trim(postString('telephone') ?? '');
            $password = postString('password') ?? '';

            if (!preg_match('/^\d{8}$/', $cin) || $nom_prenom === '' || strlen($nom_prenom) > 150 || !preg_match('/^[0-9+()\s-]{8,20}$/', $telephone) || strlen($password) < 8) {
                $erreur = "Veuillez vérifier les informations saisies. Le mot de passe doit contenir au moins 8 caractères.";
                require_once 'views/inscription_citoyen.php';
                return;
            }

            $result = $this->model->ajouterCitoyen($cin, $nom_prenom, $telephone, $password);
            if ($result) {
                header("Location: index.php?action=login_citoyen&success=1");
                exit();
            }

            $erreur = "Impossible de créer le compte. Vérifiez les informations saisies ou réessayez.";
            require_once 'views/inscription_citoyen.php';
            return;
        }

        require_once 'views/inscription_citoyen.php';
    }

    public function loginAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verifyCsrfToken();
            $cin = trim(postString('cin') ?? '');
            $password = postString('password') ?? '';
            $citoyen = $this->model->getCitoyenByCin($cin);

            if ($citoyen && ($citoyen['etat'] ?? 'actif') !== 'bloque' && password_verify($password, $citoyen['password'])) {
                session_regenerate_id(true);
                $_SESSION['id_citoyen'] = $citoyen['id_citoyen'];
                $_SESSION['nom_prenom'] = $citoyen['nom_prenom'];
                $_SESSION['role'] = 'citoyen';
                header("Location: index.php?action=dashboard_citoyen");
                exit();
            }

            $erreur = ($citoyen && ($citoyen['etat'] ?? '') === 'bloque') ? "Votre compte est bloqué." : "Numéro de CIN ou mot de passe incorrect.";
            require_once 'views/login_citoyen.php';
            return;
        }

        require_once 'views/login_citoyen.php';
    }

    public function dashboardAction() {
        if (!isset($_SESSION['id_citoyen']) || ($_SESSION['role'] ?? '') !== 'citoyen') {
            header("Location: index.php?action=login_citoyen");
            exit();
        }
        require_once 'views/dashboard_citoyen.php';
    }
}
?>