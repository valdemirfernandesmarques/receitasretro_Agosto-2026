<?php
session_start();
include_once("../includes/conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    var_dump($sql);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute(); 

    $resultado = $stmt->get_result();
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
            ($usuario["senha"]);
        if ($senha == $usuario["senha"]) {
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nome"] = $usuario["nome"];
            header("Location: receitas.php");// testar direcionamento aqui
            exit();
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Receitas Retrô</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login Administrativo</h2>
     <!--   <?php if (!empty($erro)) echo "<p style='color:red;'>$erro</p>"; var_dump($usuario["senha"]); ?> --> 
        <form method="POST" >
            <input type="email" name="email" placeholder="E-mail" required><br>
            <input type="password" name="senha" placeholder="Senha" required><br>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>