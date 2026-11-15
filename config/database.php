<?php
function getPdoConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $host = getenv('DB_HOST') ?: 'localhost';
        $name = getenv('DB_NAME') ?: 'gestion_onas';
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        if ($user === false || $pass === false) {
            throw new RuntimeException('Les variables DB_USER et DB_PASS doivent être configurées.');
        }

        $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    return $pdo;
}
?>