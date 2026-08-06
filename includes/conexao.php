<?php
$servername = getenv('DB_HOST') ?: "b8wru79itthvwibb49hs-mysql.services.clever-cloud.com";
$username   = getenv('DB_USER') ?: "uatrkaejrrhqpjnk";
$password   = getenv('DB_PASS') ?: "DrBXMENnZTaHi8nAiekH";
$dbname     = getenv('DB_NAME') ?: "b8wru79itthvwibb49hs";
$port       = getenv('DB_PORT') ?: 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Configura a conexão para 'latin1' temporariamente para ler a codificação bruta original armazenada
$conn->set_charset("latin1");

// Função auxiliar para reparar strings codificadas duplamente (Mojibake)
if (!function_exists('corrigir_texto')) {
    function corrigir_texto($texto) {
        if (empty($texto)) return '';
        // Se a string contém marcadores típicos de UTF-8 lido como Latin1
        if (preg_match('/[\xC2\xC3]/', $texto)) {
            return mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
        }
        return mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');
    }
}
?>