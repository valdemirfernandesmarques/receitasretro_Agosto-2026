<?php
$servername = getenv('DB_HOST') ?: "b8wru79itthvwibb49hs-mysql.services.clever-cloud.com";
$username   = getenv('DB_USER') ?: "uatrkaejrrhqpjnk";
$password   = getenv('DB_PASS') ?: "DrBXMENnZTaHi8nAiekH";
$dbname     = getenv('DB_NAME') ?: "b8wru79itthvwibb49hs";
$port       = getenv('DB_PORT') ?: 3306;

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Checar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Configurar o charset para utf8
$conn->set_charset("utf8");
?>