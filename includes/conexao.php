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

// Configura o conjunto de caracteres para UTF-8 de forma nativa e completa (utf8mb4)
if (!$conn->set_charset("utf8mb4")) {
    // Fallback caso utf8mb4 não esteja disponível
    $conn->set_charset("utf8");
}

// Função auxiliar para tratar e garantir strings em UTF-8 válido
if (!function_exists('corrigir_texto')) {
    function corrigir_texto($texto) {
        if (empty($texto)) return '';
        // Garante que o texto esteja codificado corretamente em UTF-8
        return mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
    }
}
?>