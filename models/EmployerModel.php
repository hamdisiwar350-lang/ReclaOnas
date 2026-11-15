<?php
require_once __DIR__ . '/../config/database.php';

function getEmployerByMatricule(string $matricule): ?array {
    $stmt = getPdoConnection()->prepare('SELECT * FROM employer_onas WHERE matricule = :matricule LIMIT 1');
    $stmt->execute([':matricule' => trim($matricule)]);
    $employer = $stmt->fetch(PDO::FETCH_ASSOC);
    return $employer ?: null;
}

function updateStatutReclamation(int $id, string $statut): bool {
    $statutsAutorises = ['En attente', 'En cours', 'Terminée', 'Traité'];
    if (!in_array($statut, $statutsAutorises, true)) {
        throw new InvalidArgumentException('Statut invalide.');
    }
    $stmt = getPdoConnection()->prepare('UPDATE reclamation SET statut = :statut WHERE id_reclamation = :id');
    return $stmt->execute([':statut' => $statut, ':id' => $id]);
}

function getToutesLesChefEquipes(): array {
    $stmt = getPdoConnection()->query("SELECT * FROM employer_onas WHERE role = 'equipe' ORDER BY id_employer DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function validerIntervention(int $id_reclamation, string $rapport, ?string $photo_intervention): bool {
    if ($rapport === '') {
        throw new InvalidArgumentException('Le rapport est obligatoire.');
    }
    $sql = "UPDATE reclamation SET statut = 'Terminée', rapport = :rapport, photo_intervention = :photo WHERE id_reclamation = :id";
    $stmt = getPdoConnection()->prepare($sql);
    return $stmt->execute([':rapport' => $rapport, ':photo' => $photo_intervention, ':id' => $id_reclamation]);
}
?>