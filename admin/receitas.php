
<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit;
}

include_once '../includes/conexao.php';

// Atualizar status da receita (ativar/desativar)
if (isset($_GET['acao']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $acao = $_GET['acao'] === 'ativar' ? 1 : 0;
    $sql = "UPDATE receitas SET ativo = $acao WHERE id = $id";
    mysqli_query($conn, $sql);
    header('Location: receitas.php');
    exit;
}

// Buscar todas as receitas
$sql = "SELECT r.id, r.titulo, r.categoria, r.ativo, u.nome AS autor FROM receitas r JOIN usuarios u ON r.usuario_id = u.id";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gerenciar Receitas</title>
</head>
<body>
    <h1>Painel de Receitas</h1>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Título</th>
                <th>Categoria</th>
                <th>Autor</th>
                <th>Status</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['titulo']; ?></td>
                <td><?php echo $row['categoria']; ?></td>
                <td><?php echo $row['autor']; ?></td>
                <td><?php echo $row['ativo'] ? 'Ativa' : 'Inativa'; ?></td>
                <td>
                    <?php if ($row['ativo']): ?>
                        <a href="?acao=desativar&id=<?php echo $row['id']; ?>">Desativar</a>
                    <?php else: ?>
                        <a href="?acao=ativar&id=<?php echo $row['id']; ?>">Ativar</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
