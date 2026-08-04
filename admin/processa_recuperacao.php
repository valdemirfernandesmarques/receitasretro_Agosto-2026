<?php
// Arquivo: receitasretro/admin/processa_recuperacao.php

session_start();
require_once '../config.php';
require_once '../funcoes.php';
require_once '../includes/conexao.php';

// Limpa o log a cada execução (CUIDADO EM PRODUÇÃO!)
// Mas para debug é MUITO útil. Remova isso depois.
// error_log("--- Nova Tentativa de Recuperacao - " . date('Y-m-d H:i:s') . " ---");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    error_log("DEBUG: E-mail recebido do formulario: " . ($email ? $email : 'EMAIL INVALIDO OU VAZIO') . " (Valor RAW: " . (isset($_POST['email']) ? $_POST['email'] : 'N/A') . ")");

    if (!$email) {
        $_SESSION['mensagem'] = 'Por favor, insira um e-mail válido.';
        header('Location: ../recuperar_senha.php');
        exit();
    }

    if ($conn->connect_error) {
        error_log("DEBUG: Erro de conexao com o DB: " . $conn->connect_error);
        $_SESSION['mensagem'] = 'Ocorreu um erro interno ao processar sua solicitação. Por favor, tente novamente mais tarde.';
        header('Location: ../recuperar_senha.php');
        exit();
    }

    $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE email = ? AND ativo = 1");
    if (!$stmt) {
        error_log("DEBUG: Erro ao preparar statement (SELECT usuarios): " . $conn->error);
        $_SESSION['mensagem'] = 'Ocorreu um erro interno ao processar sua solicitação. Por favor, tente novamente mais tarde.';
        header('Location: ../recuperar_senha.php');
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();

    if ($usuario) {
        error_log("DEBUG: Usuario ENCONTRADO no DB: ID=" . $usuario['id'] . ", Nome=" . $usuario['nome']);

        // A PARTIR DAQUI SE O USUÁRIO É ENCONTRADO
        $token = bin2hex(random_bytes(32));
        $expira_em = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Limpar tokens antigos para este usuário
        $stmt_delete = $conn->prepare("DELETE FROM tokens_recuperacao WHERE usuario_id = ?");
        if (!$stmt_delete) {
            error_log("DEBUG: Erro ao preparar statement (DELETE tokens_recuperacao): " . $conn->error);
            $_SESSION['mensagem'] = 'Ocorreu um erro ao gerar o link de recuperação. Por favor, tente novamente.';
            header('Location: ../recuperar_senha.php');
            exit();
        }
        $stmt_delete->bind_param("i", $usuario['id']);
        $stmt_delete->execute();
        $stmt_delete->close();

        // Inserir novo token
        $stmt_insert = $conn->prepare("INSERT INTO tokens_recuperacao (usuario_id, token, expira_em) VALUES (?, ?, ?)");
        if (!$stmt_insert) {
            error_log("DEBUG: Erro ao preparar statement (INSERT tokens_recuperacao): " . $conn->error);
            $_SESSION['mensagem'] = 'Ocorreu um erro ao gerar o link de recuperação. Por favor, tente novamente.';
            header('Location: ../recuperar_senha.php');
            exit();
        }
        $stmt_insert->bind_param("iss", $usuario['id'], $token, $expira_em);
        $stmt_insert->execute();
        
        // --- INÍCIO DEBUG
        if ($stmt_insert->affected_rows > 0) {
            error_log("DEBUG: Token salvo no DB com sucesso. Token: " . $token);
        } else {
            error_log("DEBUG: ERRO: Token NAO salvo no DB. affected_rows = " . $stmt_insert->affected_rows . ", Erro: " . $stmt_insert->error);
        }
        // --- FIM DEBUG

        $stmt_insert->close();


        // Tentar enviar o e-mail
        error_log("DEBUG: Tentando enviar email para: " . $email . " (" . $usuario['nome'] . ")");
        if (enviarEmailRedefinicao($email, $usuario['nome'], $token)) {
            error_log("DEBUG: Funcao enviarEmailRedefinicao retornou TRUE.");
            $_SESSION['mensagem'] = 'Um link de recuperação de senha foi enviado para o seu e-mail. Verifique sua caixa de entrada (e a pasta de spam!).';
        } else {
            error_log("DEBUG: Funcao enviarEmailRedefinicao retornou FALSE. Verifique error_log em funcoes.php.");
            $_SESSION['mensagem'] = 'Erro ao enviar o e-mail de recuperação. Por favor, tente novamente mais tarde.';
        }
    } else {
        error_log("DEBUG: Usuario NAO ENCONTRADO no DB para email: " . $email . ". Ativo = 1 nao corresponde ou email nao existe.");
        $_SESSION['mensagem'] = 'Se o e-mail estiver cadastrado em nossa base, um link de recuperação foi enviado para ele.';
    }

    $conn->close();
    header('Location: ../recuperar_senha.php');
    exit();
} else {
    error_log("DEBUG: Acesso direto a processa_recuperacao.php sem POST.");
    header('Location: ../recuperar_senha.php');
    exit();
}
?>