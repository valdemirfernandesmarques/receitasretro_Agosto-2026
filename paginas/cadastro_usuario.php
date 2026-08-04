<?php
require_once '../includes/conexao.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);

    // Verifica se e-mail já está cadastrado
    $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Este e-mail já está cadastrado.'); window.location.href='cadastro.php';</script>";
        exit();
    }

    // Insere novo usuário com status 'pendente'
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, status) VALUES (?, ?, ?, 'liberado')");
    $stmt->bind_param("sss", $nome, $email, $senha);
    $stmt->execute();

    echo "<script>alert('Cadastro realizado com sucesso! Agora faça seu login e adicione sua receita.'); window.location.href='login.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
</head>
<body class="pagina-cadastro">

    <h2>Cadastro de Usuário</h2>
    <form method="post" action="">
        <label for="nome">Nome:</label><br>
        <input type="text" name="nome" required><br><br>
        <label for="email">E-mail:</label><br>
        <input type="email" name="email" required><br><br>
        <label for="senha">Senha:</label><br>
        <input type="password" name="senha" required><br><br>
        <button type="submit">Cadastrar</button>
    </form>
    
</body>
</html>