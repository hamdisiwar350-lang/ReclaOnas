<?php
require_once __DIR__ . '/../models/ReclamationModel.php';

function afficherListe() {
    if (!isset($_SESSION['id_citoyen']) || ($_SESSION['role'] ?? '') !== 'citoyen') {
        header('Location: index.php?action=login_citoyen');
        exit;
    }

    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM reclamation WHERE id_citoyen = :id ORDER BY id_reclamation DESC");
    $stmt->execute([':id' => $_SESSION['id_citoyen']]);
    $reclamations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    require_once 'views/afficher_reclamations.php';
}

function gererAjout() {
    if (!isset($_SESSION['id_citoyen']) || ($_SESSION['role'] ?? '') !== 'citoyen') {
        header('Location: index.php?action=login_citoyen');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $typesAutorises = ['Bouchon sur réseau', 'Bouchon sur branchement et retour des eaux', 'Affaissement, casse du réseau', 'Odeurs', 'Débordements d\'eau usées', 'Autres'];
        $type = trim($_POST['type_probleme'] ?? '');
        $repere = trim($_POST['repere'] ?? '');
        $latitude = trim($_POST['latitude'] ?? '');
        $longitude = trim($_POST['longitude'] ?? '');

        if (!in_array($type, $typesAutorises, true) || $repere === '' || !is_numeric($latitude) || !is_numeric($longitude) || abs((float) $latitude) > 90 || abs((float) $longitude) > 180) {
            http_response_code(400);
            exit('Données de réclamation invalides.');
        }

        $urgences = ['Odeurs', 'Débordements d\'eau usées', 'Affaissement, casse du réseau', 'Bouchon sur branchement et retour des eaux'];
        $priorite = in_array($type, $urgences, true) ? 'Urgente' : 'Normale';
        $photo_path = null;
        $photoBase64 = $_POST['photo_base64'] ?? '';

        if ($photoBase64 !== '') {
            if (!preg_match('#^data:image/(jpeg|png|webp);base64,([A-Za-z0-9+/=]+)$#', $photoBase64, $matches)) {
                http_response_code(400);
                exit('Image invalide.');
            }

            $imageData = base64_decode($matches[2], true);
            if ($imageData === false || strlen($imageData) > 5 * 1024 * 1024 || @getimagesizefromstring($imageData) === false) {
                http_response_code(400);
                exit('Image invalide ou trop volumineuse.');
            }

            $dossier_upload = __DIR__ . '/../uploads/';
            if (!is_dir($dossier_upload) && !mkdir($dossier_upload, 0755, true)) {
                http_response_code(500);
                exit('Impossible de préparer le stockage.');
            }

            $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
            $nom_fichier = bin2hex(random_bytes(16)) . '.' . $extension;
            if (file_put_contents($dossier_upload . $nom_fichier, $imageData) === false) {
                http_response_code(500);
                exit('Échec de l’enregistrement de l’image.');
            }
            $photo_path = 'uploads/' . $nom_fichier;
        }

        ajouterReclamationBDD((int) $_SESSION['id_citoyen'], $type, $repere, $photo_path, $latitude, $longitude, $priorite);
        header('Location: index.php?action=afficher');
        exit;
    }

    require_once 'views/ajouter_reclamation.php';
}

function requireAdminReclamationAccess(): void {
    if (!isset($_SESSION['id_employer']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: index.php?action=login_employer');
        exit;
    }
}

function modifierReclamationForm(int $id) {
    requireAdminReclamationAccess();
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM reclamation WHERE id_reclamation = :id");
    $stmt->execute([':id' => $id]);
    $recla = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recla) {
        http_response_code(404);
        exit('Réclamation introuvable.');
    }
    require_once 'views/modifier_reclamation.php';
}

function updateReclamationAction(int $id) {
    requireAdminReclamationAccess();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Méthode non autorisée.');
    }

    $type = trim($_POST['type'] ?? '');
    $repere = trim($_POST['repere'] ?? '');
    if ($type === '' || $repere === '') {
        http_response_code(400);
        exit('Données invalides.');
    }

    updateReclamation($id, $type, $repere);
    header("Location: index.php?action=dashboard_admin");
    exit;
}

function supprimerReclamationAction(int $id) {
    requireAdminReclamationAccess();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Méthode non autorisée.');
    }

    supprimerReclamation($id);
    header("Location: index.php?action=dashboard_admin");
    exit;
}
?>