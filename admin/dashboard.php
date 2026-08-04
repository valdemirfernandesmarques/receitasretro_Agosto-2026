<?php
session_start();
require_once '../includes/conexao.php';
include_once('../includes/header.php');

// Verifica se é o admin logado (continua sendo importante)
if (!isset($_SESSION["usuario_email"]) || $_SESSION["usuario_email"] !== "admin@retro.com") {
    header("Location: ../paginas/login.php");
    exit();
}

// Lógica de Liberação de usuários (mantida, pois não é o foco da mudança)
if (isset($_GET['liberar_id'])) {
    $liberar_id = intval($_GET['liberar_id']);
    $stmt = $conn->prepare("UPDATE usuarios SET status = 'liberado' WHERE id = ?");
    $stmt->bind_param("i", $liberar_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

// REMOVIDA: A lógica de Liberação de receitas via GET será transferida para processar_receita.php
// if (isset($_GET['liberar_receita_id'])) {
//     $liberar_receita_id = intval($_GET['liberar_receita_id']);
//     $stmt = $conn->prepare("UPDATE receitas SET status = 'liberado' WHERE id = ?");
//     $stmt->bind_param("i", $liberar_receita_id);
//     $stmt->execute();
//     header("Location: dashboard.php");
//     exit();
// }

// --- Exibição de mensagens de feedback (NOVO) ---
// Adiciona um div para exibir mensagens de sucesso ou erro vindas de processar_receita.php
// Isso melhora a experiência do administrador ao saber o resultado da ação.
$mensagem_admin = '';
if (isset($_SESSION['mensagem_admin'])) {
    $mensagem_admin = $_SESSION['mensagem_admin'];
    unset($_SESSION['mensagem_admin']); // Limpa a mensagem da sessão após exibir
}
// --- Fim da Exibição de mensagens de feedback ---


// Busca todos os usuários
$resultado_usuarios = $conn->query("SELECT id, nome, email, status FROM usuarios ORDER BY status DESC");

// Busca receitas pendentes
$sql_receitas = "
    SELECT r.id, r.titulo, u.nome AS nome_usuario, u.email 
    FROM receitas r
    JOIN usuarios u ON r.usuario_id = u.id
    WHERE r.status = 'pendente'
    ORDER BY r.id DESC
";
$resultado_receitas = $conn->query($sql_receitas);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="pagina-dashboard">

    <?php if (!empty($mensagem_admin)): ?>
        <div style="padding: 10px; margin: 20px auto; max-width: 90%; text-align: center; border-radius: 5px; 
            background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
            <?= htmlspecialchars($mensagem_admin) ?>
        </div>
    <?php endif; ?>

    <h2>Usuários Cadastrados</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>

        <?php while ($usuario = $resultado_usuarios->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($usuario['nome']) ?></td>
                <td><?= htmlspecialchars($usuario['email']) ?></td>
                <td><?= $usuario['status'] ?></td>
                <td>
                    <?php if ($usuario['status'] == 'pendente') { ?>
                        <a href="?liberar_id=<?= $usuario['id'] ?>" onclick="return confirm('Deseja liberar este usuário?')">Liberar</a>
                    <?php } else { ?>
                        Liberado
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <br><hr><br>

    <h2>Receitas Pendentes</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>Título da Receita</th>
            <th>Nome do Usuário</th>
            <th>E-mail do Usuário</th>
            <th>Ações</th>
        </tr>

        <?php while ($receita = $resultado_receitas->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($receita['titulo']) ?></td>
                <td><?= htmlspecialchars($receita['nome_usuario']) ?></td>
                <td><?= htmlspecialchars($receita['email']) ?></td>
                <td>
                    <a href="ver_receita_pendente.php?id=<?= htmlspecialchars($receita['id']) ?>" class="btn-ver-receita">Ver Receita</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    </body>
<?php include_once('../includes/footer.php'); ?>