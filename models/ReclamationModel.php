<?php
require_once __DIR__ . '/../config/database.php';

function ajouterReclamationBDD(int $id_citoyen, string $type, ?string $repere, ?string $photo, ?string $latitude, ?string $longitude, string $priorite){
    $stmt = getPdoConnection()->prepare("INSERT INTO reclamation (id_citoyen, type_probleme, repere, photo, latitude, longitude, priorite, statut) VALUES (:id_citoyen, :type_probleme, :repere, :photo, :latitude, :longitude, :priorite, 'En attente')");
    return $stmt->execute([':id_citoyen'=>$id_citoyen, ':type_probleme'=>$type, ':repere'=>$repere, ':photo'=>$photo, ':latitude'=>$latitude, ':longitude'=>$longitude, ':priorite'=>$priorite]);
}
function getReclamationsParCitoyen($id_citoyen) { $stmt=getPdoConnection()->prepare('SELECT * FROM reclamation WHERE id_citoyen = :id_citoyen ORDER BY id_reclamation DESC'); $stmt->execute([':id_citoyen'=>$id_citoyen]); return $stmt->fetchAll(PDO::FETCH_ASSOC); }
function getAllReclamations() { $stmt=getPdoConnection()->query('SELECT r.*, c.nom_prenom AS nom_citoyen, c.cin FROM reclamation r JOIN citoyen c ON r.id_citoyen = c.id_citoyen ORDER BY r.id_reclamation DESC'); return $stmt->fetchAll(PDO::FETCH_ASSOC); }
function supprimerReclamation(int $id_reclamation) { $stmt=getPdoConnection()->prepare('DELETE FROM reclamation WHERE id_reclamation = :id'); return $stmt->execute([':id'=>$id_reclamation]); }
function updateReclamation(int $id_reclamation, string $type, string $repere) { $stmt=getPdoConnection()->prepare('UPDATE reclamation SET type_probleme = :type, repere = :repere WHERE id_reclamation = :id'); return $stmt->execute([':id'=>$id_reclamation, ':type'=>$type, ':repere'=>$repere]); }
function getReclamationsParChefEquipe($id_equipe) { $stmt=getPdoConnection()->prepare('SELECT * FROM reclamation WHERE TRIM(affecte_a) = :id_equipe ORDER BY id_reclamation DESC'); $stmt->execute([':id_equipe'=>trim((string)$id_equipe)]); return $stmt->fetchAll(PDO::FETCH_ASSOC); }
function reclamationEstAssigneeEquipe(int $id_reclamation, int $id_equipe): bool { $stmt=getPdoConnection()->prepare('SELECT 1 FROM reclamation WHERE id_reclamation = :id AND TRIM(affecte_a) = :equipe LIMIT 1'); $stmt->execute([':id'=>$id_reclamation, ':equipe'=>(string)$id_equipe]); return (bool)$stmt->fetchColumn(); }
function affecterReclamationAdmin(int $id_reclamation, int $id_equipe) {
    $sql = "UPDATE reclamation SET affecte_a = :id_equipe, statut = 'En cours' WHERE id_reclamation = :id AND EXISTS (SELECT 1 FROM employer_onas WHERE id_employer = :id_equipe_check AND role = 'equipe')";
    $stmt = getPdoConnection()->prepare($sql);
    $stmt->execute([':id_equipe'=>$id_equipe, ':id'=>$id_reclamation, ':id_equipe_check'=>$id_equipe]);
    if ($stmt->rowCount() > 0) {
        return true;
    }

    $check = getPdoConnection()->prepare('SELECT 1 FROM reclamation WHERE id_reclamation = :id AND TRIM(affecte_a) = :id_equipe LIMIT 1');
    $check->execute([':id' => $id_reclamation, ':id_equipe' => (string) $id_equipe]);
    return (bool) $check->fetchColumn();
}
?>