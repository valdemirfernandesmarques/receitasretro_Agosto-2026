<?php
require_once '../includes/auth.php'; // Verifica se o usuário está autenticado
require_once '../includes/header.php';
require_once '../includes/db_connect.php';

// Consulta usuários cadastrados
$sql = "SELECT id, nome, email, telefone, status FROM usuarios ORDER BY nome ASC";
$result = $conn->query($sql);
?>

<div class="container">
<link rel="stylesheet" href="../assets/css/style.css">
    <h2>Gerenciar Usuários</h2>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($usuario = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['telefone']) ?></td>
                    <td><?= $usuario['status'] == 1 ? 'Ativo' : 'Inativo' ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>">Editar</a> |
                        <a href="excluir_usuario.php?id=<?= $usuario['id'] ?>" onclick="return confirm('Deseja realmente excluir este usuário?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>