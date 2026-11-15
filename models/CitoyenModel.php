<?php
require_once __DIR__ . '/../config/database.php';

class CitoyenModel {
    public function ajouterCitoyen(string $cin, string $nom_prenom, string $telephone, string $password): bool {
        $pdo = getPdoConnection();
        $stmt_check = $pdo->prepare('SELECT 1 FROM citoyen WHERE cin = ? LIMIT 1');
        $stmt_check->execute([$cin]);
        if ($stmt_check->fetchColumn()) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new RuntimeException('Impossible de sécuriser le mot de passe.');
        }

        $stmt = $pdo->prepare("INSERT INTO citoyen (cin, nom_prenom, telephone, password, etat) VALUES (?, ?, ?, ?, 'actif')");
        return $stmt->execute([$cin, $nom_prenom, $telephone, $hash]);
    }

    public function getCitoyenByCin(string $cin): ?array {
        $stmt = getPdoConnection()->prepare('SELECT * FROM citoyen WHERE cin = ? LIMIT 1');
        $stmt->execute([$cin]);
        $citoyen = $stmt->fetch(PDO::FETCH_ASSOC);
        return $citoyen ?: null;
    }
}
?>