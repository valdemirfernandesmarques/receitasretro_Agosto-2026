<?php
$servername = "receitas_retro.mysql.dbaas.com.br";
$username = "receitas_retro";
$password = "Receitas@12";
$dbname = "receitas_retro";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>