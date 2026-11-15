<?php
if (!function_exists('getPdoConnection')) {
    function getPdoConnection(): PDO
    {
        throw new RuntimeException('getPdoConnection() is not defined.');
    }
}

function getEmployerByMatricule($matricule) {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT * FROM employer_onas WHERE matricule = :matricule");
    $stmt->execute([':matricule' => $matricule]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateStatutReclamation(int $id, string $statut) {
    $pdo = getPdoConnection();
    $sql = "UPDATE reclamation SET statut = :statut WHERE id_reclamation = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':statut' => $statut, ':id' => $id]);
}

function getToutesLesChefEquipes() {
    $pdo = getPdoConnection();
    $sql = "SELECT * FROM employer_onas WHERE role = 'equipe'";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 🆕 دالة جديدة لإنهاء الشكاية وتسجيل التقرير (نظيفة وفي بلاصتها)
// دالة إنهاء الشكاية وتحديث الجدول مباشرة
function validerIntervention($id_reclamation, $rapport, $photo_intervention) {
    $pdo = getPdoConnection();
    
    $sql = "UPDATE reclamation 
            SET statut = 'Terminée', 
                rapport = :rapport, 
                photo_intervention = :photo 
            WHERE id_reclamation = :id";
            
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':rapport' => $rapport,
        ':photo'   => $photo_intervention,
        ':id'      => $id_reclamation
    ]);
}
?>