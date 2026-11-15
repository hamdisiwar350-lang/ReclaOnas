<?php
class CitoyenModel {
    // تسجيل مواطن جديد
    public function ajouterCitoyen($cin, $nom_prenom, $telephone, $password) {
        $pdo = getPdoConnection();
        
        // نثبتو الساعة كان الـ CIN موجود من قبل
        $stmt_check = $pdo->prepare("SELECT cin FROM citoyen WHERE cin = ?");
        $stmt_check->execute([$cin]);
        if ($stmt_check->fetch()) {
            return false; // الـ CIN مستعمل
        }

        // إضافة المواطن
        $sql = "INSERT INTO citoyen (cin, nom_prenom, telephone, password, etat) VALUES (?, ?, ?, ?, 'actif')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cin, $nom_prenom, $telephone, password_hash($password, PASSWORD_DEFAULT)]);
    }

    // جلب بيانات المواطن بالـ CIN (لـ Login)
    public function getCitoyenByCin($cin) {
        $pdo = getPdoConnection();
        $stmt = $pdo->prepare("SELECT * FROM citoyen WHERE cin = ?");
        $stmt->execute([$cin]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>