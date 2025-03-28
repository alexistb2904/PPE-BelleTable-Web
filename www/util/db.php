<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$dbDatabase = $_ENV['USEALTERNATE'] ? $_ENV['ALT_DB_DATABASE'] : $_ENV['DB_DATABASE'];
$dbHost = $_ENV['USEALTERNATE'] ? $_ENV['ALT_DB_HOST'] : $_ENV['DB_HOST'];
$dbPort = $_ENV['USEALTERNATE'] ? $_ENV['ALT_DB_PORT'] : $_ENV['DB_PORT'];
$dbUsername = $_ENV['USEALTERNATE'] ? $_ENV['ALT_DB_USERNAME'] : $_ENV['DB_USERNAME'];
$dbPassword = $_ENV['USEALTERNATE'] ? $_ENV['ALT_DB_PASSWORD'] : $_ENV['DB_PASSWORD'];
//connexion à la base de données
$dsn = 'mysql:dbname=' . $dbDatabase . ';host=' . $dbHost . ';port=' . $dbPort;

//Connexion au serveur MySQL
try {
    global $cnx;
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    );

    $cnx = new PDO($dsn, $dbUsername, $dbPassword, $options);
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
