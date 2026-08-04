<?php
// Arquivo: includes/auth.php
// Função: Verificar se o usuário está logado e/ou é administrador

// Inicia a sessão (caso ainda não tenha sido iniciada)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função para verificar se o usuário está logado
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        // Se o usuário não estiver logado, redireciona para a página de login
        header('Location: /admin/login.php');
        exit();
    }
}

// Função para verificar se o usuário é administrador
function verificarAdmin() {
    if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
        // Se não for admin, redireciona para a página inicial ou exibe acesso negado
        header('Location: /index.php');
        exit();
    }
}
?>