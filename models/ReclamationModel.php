<?php
require_once __DIR__ . '/../config/database.php';

// 1. إضافة شكاية جديدة (مع الصورة، الـ GPS، والفرز الذكي)
function ajouterReclamationBDD(int $id_citoyen, string $type, ?string $repere, ?string $photo, ?string $latitude, ?string $longitude, string $priorite){
     $pdo = getPdoConnection(); 
     $sql = "INSERT INTO reclamation (id_citoyen, type_probleme, repere, photo, latitude, longitude, priorite, statut) 
             VALUES (:id_citoyen, :type_probleme, :repere, :photo, :latitude, :longitude, :priorite, 'En attente')";
     
     $stmt = $pdo->prepare($sql);
     return $stmt->execute([
        ':id_citoyen' => $id_citoyen,
        ':type_probleme' => $type,
        ':repere' => $repere,
        ':photo' => $photo,
        ':latitude' => $latitude,
        ':longitude' => $longitude,
        ':priorite' => $priorite
     ]);
}

// 2. جلب شكايات مواطن معين
function getReclamationsParCitoyen($id_citoyen) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM reclamation WHERE id_citoyen = :id_citoyen ORDER BY id_reclamation DESC");
    $stmt->execute([':id_citoyen' => $id_citoyen]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllReclamations() {
    $pdo = getPdoConnection();
    $sql = "SELECT r.*, c.nom_prenom AS nom_citoyen, c.cin 
            FROM reclamation r 
            JOIN citoyen c ON r.id_citoyen = c.id_citoyen 
            ORDER BY r.id_reclamation DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// 4. حذف شكاية
function supprimerReclamation(int $id_reclamation) {
    $pdo = getPdoConnection();
    // تم إصلاح اسم الجدول من reclamatsion إلى reclamation
    $sql = "DELETE FROM reclamation WHERE id_reclamation = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':id' => $id_reclamation]);
}

// 5. تعديل شكاية
function updateReclamation(int $id_reclamation, string $type, string $repere) {
    $pdo = getPdoConnection();
    $sql = "UPDATE reclamation SET type_probleme = :type, repere = :repere WHERE id_reclamation = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':id' => $id_reclamation, ':type' => $type, ':repere' => $repere]);
}

// 6. جلب شكايات فريق معين

function getReclamationsParChefEquipe($id_equipe) {
    $pdo = getPdoConnection();
    // استعملنا TRIM و intval لضمان تطابق الرقم حتى لو كان مسجل كـ Text في القاعدة
    $sql = "SELECT * FROM reclamation WHERE TRIM(affecte_a) = :id_equipe ORDER BY id_reclamation DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_equipe' => trim($id_equipe)]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 7. تعيين شكاية لفريق
function affecterReclamationAdmin(int $id_reclamation, int $id_equipe) {
    $pdo = getPdoConnection();
    $sql = "UPDATE reclamation SET affecte_a = :id_equipe, statut = 'En cours' WHERE id_reclamation = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':id_equipe' => $id_equipe, ':id' => $id_reclamation]);
}
?>