<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../paginas/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador - Receitas Retrô</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #fff9f0;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 40px;
        }

        .painel-box {
            background-color: #fff;
            padding: 30px;
            margin: 0 auto;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }

        .painel-box h1 {
            color: #8b0000;
        }

        .painel-box p {
            margin-top: 10px;
            font-size: 18px;
            color: #333;
        }

        .logout-btn {
            margin-top: 20px;
            padding: 10px 25px;
            background-color: #8b0000;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }

        .logout-btn:hover {
            background-color: #a50000;
        }
    </style>
</head>
<body>
    <div class="painel-box">
        <h1>🍲 Painel Administrativo</h1>
        <p>Bem-vindo(a), <strong><?php echo $_SESSION['usuario_nome']; ?></strong>!</p>
        <p>Você está logado com sucesso no sistema Receitas Retrô.</p>

        <a class="logout-btn" href="../paginas/logout.php">Sair</a>
    </div>
</body>
</html>