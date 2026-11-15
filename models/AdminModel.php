<?php
require_once __DIR__ . '/../config/database.php';

class AdminModel {
    public function updateCitoyenEtat(int $id, string $etat): bool {
        if (!in_array($etat, ['actif', 'bloque'], true)) {
            throw new InvalidArgumentException('État citoyen invalide.');
        }
        $stmt = getPdoConnection()->prepare('UPDATE citoyen SET etat = ? WHERE id_citoyen = ?');
        return $stmt->execute([$etat, $id]);
    }

    public function countTotal() {
        return getPdoConnection()->query('SELECT COUNT(*) FROM reclamation')->fetchColumn();
    }

    public function countByStatut(string $statut) {
        $stmt = getPdoConnection()->prepare('SELECT COUNT(*) FROM reclamation WHERE statut = ?');
        $stmt->execute([$statut]);
        return $stmt->fetchColumn();
    }

    public function getAllCitoyens(): array {
        return getPdoConnection()->query('SELECT * FROM citoyen ORDER BY id_citoyen DESC')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllChefsEquipe(): array {
        $stmt = getPdoConnection()->query("SELECT * FROM employer_onas WHERE role = 'equipe' ORDER BY id_employer DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouterChefEquipe(string $matricule, string $nom, string $email, string $password, string $specialite): bool {
        $sql = "INSERT INTO employer_onas (matricule, nom, email, password, specialite, role) VALUES (?, ?, ?, ?, ?, 'equipe')";
        $stmt = getPdoConnection()->prepare($sql);
        return $stmt->execute([$matricule, $nom, $email, password_hash($password, PASSWORD_DEFAULT), $specialite]);
    }

    public function supprimerChefEquipe(int $id): bool {
        $stmt = getPdoConnection()->prepare("DELETE FROM employer_onas WHERE id_employer = ? AND role = 'equipe'");
        return $stmt->execute([$id]);
    }
}
?>