<?php
// Arquivo: paginas/login.php

session_start(); // Inicia a sessão para gerenciar variáveis de sessão (ex: dados do usuário logado, mensagens)

// Inclui o arquivo de conexão com o banco de dados.
// O caminho '../includes/conexao.php' é usado porque 'login.php' está em 'paginas/'
// e 'conexao.php' está em 'includes/', um nível acima e dentro da pasta 'includes'.
require_once '../includes/conexao.php';

// Verifica se a requisição HTTP é do tipo POST, indicando que o formulário de login foi enviado.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta e sanitiza os dados do formulário. 'trim()' remove espaços em branco extras.
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    // Prepara uma consulta SQL para selecionar o ID, email, senha e status do usuário.
    // O '?' é um placeholder para evitar injeção de SQL, usando prepared statements.
    // Também adicionei 'tipo' na sua query, pois seu banco tem essa coluna e é útil para o admin.
    $stmt = $conn->prepare("SELECT id, email, senha, status, tipo FROM usuarios WHERE email = ?");
    // 'bind_param("s", $email)' associa o valor da variável $email ao placeholder '?' como uma string ('s').
    $stmt->bind_param("s", $email);
    // Executa a consulta preparada.
    $stmt->execute();
    // Obtém o resultado da consulta.
    $resultado = $stmt->get_result();

    // Verifica se exatamente um usuário foi encontrado com o email fornecido.
    if ($resultado->num_rows === 1) {
        // Pega os dados do usuário como um array associativo.
        $usuario = $resultado->fetch_assoc();

        // Verifica se a senha fornecida corresponde ao hash da senha armazenado no banco de dados.
        if (password_verify($senha, $usuario["senha"])) {
            // Se as senhas coincidem, define as variáveis de sessão para o usuário logado.
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_email"] = $usuario["email"];
            $_SESSION["usuario_status"] = $usuario["status"];
            $_SESSION["usuario_tipo"] = $usuario["tipo"]; // Armazena o tipo de usuário (ex: 'admin', 'usuario')

            // Verifica se o usuário é o administrador pelo campo 'tipo' no banco de dados.
            // Isso é mais seguro do que comparar diretamente o e-mail se você tiver muitos admins.
            if ($usuario["tipo"] === "admin") {
                // Redireciona o administrador para o painel de administração.
                header("Location: ../admin/dashboard.php");
            }
            // Se não for admin, verifica o status do usuário.
            elseif ($usuario["status"] === "liberado") {
                // Redireciona usuários "liberados" para a página inicial.
                header("Location: ../index.php");
            } else {
                // Se o status não for "liberado" (ex: "pendente"), exibe um alerta.
                echo "<script>alert('Seu cadastro está pendente. Aguarde aprovação do administrador.'); window.location.href='login.php';</script>";
            }
            exit(); // Garante que o script pare após o redirecionamento.
        }
    }

    // Se o email não for encontrado ou a senha estiver incorreta, exibe um alerta.
    echo "<script>alert('E-mail ou senha incorretos.'); window.location.href='login.php';</script>";
}
?>

<?php
// Inclui o cabeçalho padrão do site.
// O caminho '../includes/header.php' é usado porque 'login.php' está em 'paginas/'
// e 'header.php' está em 'includes/'.
include_once('../includes/header.php');
?>

<main class="login-container">
    <section class="login-box">
        <h2>Entrar na sua conta</h2>
        <form method="post" action="">
            <label for="email">E-mail:</label>
            <input type="email" name="email" required>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" required>

            <button type="submit">Entrar</button>
        </form>

        <p><a href="../recuperar_senha.php">Esqueceu sua senha?</a></p>

        </section>
</main>

<?php
// Inclui o rodapé padrão do site.
// O caminho '../includes/footer.php' é usado pela mesma razão do header.
include_once('../includes/footer.php');
?>