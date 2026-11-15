<?php
// نعيطو للـ config متاعك وين موجودة الـ getPdoConnection
require_once 'config/database.php'; 

class AdminModel {

    // 1. تحديث حالة المواطن
    // 1. تحديث حالة المواطن اعتماداً على id_citoyen
    public function updateCitoyenEtat($id, $etat) {
        $pdo = getPdoConnection(); 
        $sql = "UPDATE citoyen SET etat = ? WHERE id_citoyen = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$etat, $id]);
    }

    // 2. حساب العدد الجملي للشكايات
    public function countTotal() {
        $pdo = getPdoConnection(); // نفس الطريقة
        $stmt = $pdo->query("SELECT COUNT(*) FROM reclamation");
        return $stmt->fetchColumn();
    }

    // 3. حساب الشكايات حسب الحالة
    public function countByStatut($statut) {
        $pdo = getPdoConnection(); // نفس الطريقة
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reclamation WHERE statut = ?");
        $stmt->execute([$statut]);
        return $stmt->fetchColumn();
    }
// زيدي الفونكسيون هذي داخل class AdminModel في ملف models/AdminModel.php
public function getAllCitoyens() {
    $pdo = getPdoConnection();
    $stmt = $pdo->query("SELECT * FROM citoyen");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// 1. جلب قائمة الفرق (نجيبو كان اللي الـ role متاعهم equipe)
    public function getAllChefsEquipe() {
        $pdo = getPdoConnection();
        $stmt = $pdo->query("SELECT * FROM employer_onas WHERE role = 'equipe'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. إضافة فريق جديد (نزيدو role = 'equipe' بصفة آلية)
    public function ajouterChefEquipe($matricule, $nom, $email, $password, $specialite) {
    $pdo = getPdoConnection();
    
    // تشفير كلمة السر للحماية (حاجة مهمة برشا)
    $mdp_crypte = password_hash($password, PASSWORD_DEFAULT);
    
    // تحضير الـ requête (استعملنا ? باش نسهلو الكود)
    // لاحظ اللي زدنا 'equipe' أوتوماتيكياً في عمود الـ role
    $sql = "INSERT INTO employer_onas (matricule, nom, email, password, specialite, role) 
            VALUES (?, ?, ?, ?, ?, 'equipe')";
    
    $stmt = $pdo->prepare($sql);
    
    // التنفيذ (ربط كل ? بالمتغير متاعها بالترتيب)
    return $stmt->execute([$matricule, $nom, $email, $mdp_crypte, $specialite]);
}

    // 3. حذف فريق (نستعملو id_employer)
    public function supprimerChefEquipe($id) {
        $pdo = getPdoConnection();
        $sql = "DELETE FROM employer_onas WHERE id_employer = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    }
}
?>