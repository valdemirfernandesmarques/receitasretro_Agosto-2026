
<?php
session_start();
include_once '../includes/conexao.php';

$id = intval($_GET['id'] ?? 0);

// Buscar dados da receita
$receita = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM receitas WHERE id = $id AND ativo = 1"));

// Calcular média de avaliações
$media_result = mysqli_query($conn, "SELECT AVG(nota) as media FROM avaliacoes WHERE receita_id = $id");
$media = round(mysqli_fetch_assoc($media_result)['media'], 1);

// Se usuário enviar avaliação
if (isset($_POST['avaliar']) && isset($_SESSION['usuario_id'])) {
    $nota = intval($_POST['estrela']);
    $usuario_id = $_SESSION['usuario_id'];
    mysqli_query($conn, "INSERT INTO avaliacoes (usuario_id, receita_id, nota) VALUES ($usuario_id, $id, $nota)
                         ON DUPLICATE KEY UPDATE nota = $nota");
    header("Location: receita_detalhe.php?id=$id");
    exit;
}
?>
<h2><?php echo $receita['titulo']; ?></h2>
<p><?php echo $receita['descricao']; ?></p>
<p><strong>Média de avaliação:</strong> <?php echo $media; ?> ⭐</p>

<?php if (isset($_SESSION['usuario_id'])): ?>
<form method="POST">
    <label for="estrela">Sua avaliação:</label>
    <select name="estrela" id="estrela">
        <option value="1">1 estrela</option>
        <option value="2">2 estrelas</option>
        <option value="3">3 estrelas</option>
        <option value="4">4 estrelas</option>
        <option value="5">5 estrelas</option>
    </select>
    <button type="submit" name="avaliar">Enviar</button>
</form>
<?php else: ?>
<p><a href="/receitasretro/login.php">Faça login</a> para avaliar esta receita.</p>
<?php endif; ?>
