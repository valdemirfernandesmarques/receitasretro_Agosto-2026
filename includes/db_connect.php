<?php
// Configurações de conexão com o banco de dados
$host     = getenv('DB_HOST') ?: 'b8wru79itthvwibb49hs-mysql.services.clever-cloud.com';
$dbname   = getenv('DB_NAME') ?: 'b8wru79itthvwibb49hs';
$user     = getenv('DB_USER') ?: 'uatrkaejrrhqpjnk';
$password = getenv('DB_PASS') ?: 'DrBXMENnZTaHi8nAiekH';
$port     = getenv('DB_PORT') ?: '3306';

// Criando conexão com PDO
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password);
    // Configura o modo de erro para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Habilita uso de prepared statements seguros
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Em caso de erro, exibe mensagem e encerra
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>