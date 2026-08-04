<?php
// Configurações de conexão com o banco de dados
$host = 'receitas_retro.mysql.dbaas.com.br';             // Host do banco (normalmente localhost)
$dbname = 'receitas_retro';       // Nome do banco de dados
$user = 'receitas_retro';                  // Usuário do banco de dados (ex: root no WampServer)
$password = 'Receitas@12';                  // Senha do banco (em branco por padrão no WampServer)

// Criando conexão com PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    // Configura o modo de erro para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Habilita uso de prepared statements seguros
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Em caso de erro, exibe mensagem e encerra
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>