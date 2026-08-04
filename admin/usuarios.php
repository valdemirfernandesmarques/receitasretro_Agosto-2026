<!----------------------------------------------
--- 2ª versão do script criado em 24/04/2025 ---
----------------------------------------------->
<?php
session_start();
require_once('../includes/auth.php'); // Verifica se o admin está logado
include_once('../includes/db_connect.php');
include_once('../includes/header.php');

// Buscar todos os usuários cadastrados com PDO
try {
   // $sql = "SELECT id, nome, email, telefone, data_cadastro, aprovado FROM usuarios ORDER BY data_cadastro DESC";
    $sql = "SELECT id, nome, email, telefone, senha, criado_em FROM usuarios ORDER BY criado_em DESC";
    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar usuários: " . $e->getMessage());
}
?>

<main class="container">
<link rel="stylesheet" href="../assets/css/style.css">
    <h2>Usuários Cadastrados</h2>

    <table class="tabela-usuarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Data Cadastro</th>
             <!--   <th>Status</th>
                <th>Ações</th> -->
            </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= $usuario['id'] ?></td>
                <td><?= htmlspecialchars($usuario['nome']) ?></td>
                <td><?= htmlspecialchars($usuario['email']) ?></td>
                <td><?= htmlspecialchars($usuario['telefone']) ?></td>
             <!--   <td><?= date('d/m/Y', strtotime($usuario['data_cadastro'])) ?></td> -->
             <td><?= date('d/m/Y', strtotime($usuario['criado_em'])) ?></td>
                <td><?= $usuario['aprovado'] ? 'Aprovado' : 'Pendente' ?></td>
                <td>
                    <?php if (!$usuario['aprovado']): ?>
                        <a href="aprovar_usuario.php?id=<?= $usuario['id'] ?>" class="btn-aprovar">Aprovar</a>
                    <?php endif; ?>
                    <a href="excluir_usuario.php?id=<?= $usuario['id'] ?>" class="btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include_once('../includes/footer.php'); ?>
