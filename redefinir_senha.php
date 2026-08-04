<?php
// Arquivo: receitasretro/admin/processa_redefinicao.php

session_start();
require_once '../config.php';          // Inclui configurações globais (da raiz)
require_once '../includes/conexao.php'; // Inclui sua conexão MySQLi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    $novaSenha = $_POST['nova_senha'];
    $confirmarSenha = $_POST['confirmar_senha'];

    if (empty($token) || empty($novaSenha) || empty($confirmarSenha)) {
        $_SESSION['mensagem'] = 'Todos os campos são obrigatórios.';
        header('Location: ../redefinir_senha.php?token=' . urlencode($token));
        exit();
    }

    if ($novaSenha !== $confirmarSenha) {
        $_SESSION['mensagem'] = 'As senhas não coincidem.';
        header('Location: ../redefinir_senha.php?token=' . urlencode($token));
        exit();
    }

    if (strlen($novaSenha) < 8 || !preg_match('/[A-Z]/', $novaSenha) || !preg_match('/[a-z]/', $novaSenha) || !preg_match('/[0-9]/', $novaSenha)) {
        $_SESSION['mensagem'] = 'A nova senha deve ter no mínimo 8 caracteres, incluindo letras maiúsculas, minúsculas e números.';
        header('Location: ../redefinir_senha.php?token=' . urlencode($token));
        exit();
    }

    // Inicia a transação MySQLi
    // Adicionei a verificação de erro para begin_transaction
    if (!$conn->begin_transaction()) {
        error_log("Erro ao iniciar transação: " . $conn->error);
        $_SESSION['mensagem'] = 'Ocorreu um erro interno. Por favor, tente novamente mais tarde.';
        header('Location: ../redefinir_senha.php?token=' . urlencode($token));
        exit();
    }

    try {
        // Validar o token e verificar expiração na tabela tokens_recuperacao (MySQLi)
        $stmt_token = $conn->prepare("SELECT usuario_id, expira_em FROM tokens_recuperacao WHERE token = ?");
        if (!$stmt_token) { // Verifica se o prepare falhou
            error_log("Erro ao preparar statement (SELECT token): " . $conn->error);
            throw new Exception("Erro interno de banco de dados."); // Lança uma exceção para o catch
        }
        $stmt_token->bind_param("s", $token);
        $stmt_token->execute();
        $resultado_token = $stmt_token->get_result();
        $token_info = $resultado_token->fetch_assoc();
        $stmt_token->close();

        if ($token_info) {
            $agora = new DateTime();
            $expiracao = new DateTime($token_info['expira_em']);

            if ($agora < $expiracao) {
                $usuario_id = $token_info['usuario_id'];

                // Hash da nova senha
                $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

                // Atualizar a senha na tabela usuarios (MySQLi)
                $stmt_update_senha = $conn->prepare("UPDATE usuarios SET senha = ?, ativo = 1 WHERE id = ?");
                if (!$stmt_update_senha) { // Verifica se o prepare falhou
                    error_log("Erro ao preparar statement (UPDATE senha): " . $conn->error);
                    throw new Exception("Erro interno de banco de dados.");
                }
                $stmt_update_senha->bind_param("si", $senhaHash, $usuario_id);
                $stmt_update_senha->execute();
                $stmt_update_senha->close();

                // Remover o token da tabela tokens_recuperacao (MySQLi)
                $stmt_delete_token = $conn->prepare("DELETE FROM tokens_recuperacao WHERE token = ?");
                if (!$stmt_delete_token) { // Verifica se o prepare falhou
                    error_log("Erro ao preparar statement (DELETE token): " . $conn->error);
                    throw new Exception("Erro interno de banco de dados.");
                }
                $stmt_delete_token->bind_param("s", $token);
                $stmt_delete_token->execute();
                $stmt_delete_token->close();

                $conn->commit(); // Confirma a transação

                $_SESSION['mensagem'] = 'Sua senha foi redefinida com sucesso! Você já pode fazer login.';
                header('Location: ../paginas/login.php'); // Redireciona para a página de login
                exit();
            } else {
                $_SESSION['mensagem'] = 'O link de redefinição de senha expirou. Por favor, solicite um novo.';
                header('Location: ../recuperar_senha.php');
                exit();
            }
        } else {
            $_SESSION['mensagem'] = 'Token de redefinição de senha inválido ou já utilizado.';
            header('Location: ../recuperar_senha.php');
            exit();
        }

    } catch (Exception $e) {
        $conn->rollback(); // Desfaz a transação em caso de erro
        $_SESSION['mensagem'] = 'Ocorreu um erro ao redefinir sua senha. Por favor, tente novamente mais tarde.';
        error_log("Erro na redefinição de senha (processa_redefinicao.php): " . $e->getMessage());
        header('Location: ../redefinir_senha.php?token=' . urlencode($token));
        exit();
    } finally {
        $conn->close(); // Fechar a conexão MySQLi no final, mesmo em caso de erro
    }
} else {
    header('Location: ../recuperar_senha.php');
    exit();
}