<?php
// Configurações de conexão com o banco de dados
$host     = getenv('DB_HOST') ?: 'b8wru79itthvwibb49hs-mysql.services.clever-cloud.com';
$dbname   = getenv('DB_NAME') ?: 'b8wru79itthvwibb49hs';
$user     = getenv('DB_USER') ?: 'uatrkaejrrhqpjnk';
$password = getenv('DB_PASS') ?: 'DrBXMENnZTaHi8nAiekH';
$port     = getenv('DB_PORT') ?: '3306';

try {
    // Configura a conexão PDO para ler a codificação armazenada no banco
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $password, [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>