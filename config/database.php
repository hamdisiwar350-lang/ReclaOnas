<?php
function getPdoConnection() {
    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO("mysql:host=localhost;dbname=gestion_onas;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $pdo;
}
?>