<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
//connexion a la base de données
$dsn = 'mysql:dbname=' . $_ENV['DB_DATABASE'] . ';host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'];
//Connexion au serveur MySQL
try {
    global $cnx;
    $cnx = new PDO(
        $dsn,
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        )
    );
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
