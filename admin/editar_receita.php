<?php
// admin/editar_receita.php
require_once '../includes/db_connect.php';

if (!isset($_GET['id'])) {
    echo "ID da receita não informado.";
    exit;
}

$id = $_GET['id'];
$receita = null;

// Consulta
$stmt = $conn->prepare("SELECT * FROM receitas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $receita = $result->fetch_assoc();
} else {
    echo "Receita não encontrada.";
    exit;
}

// Atualiza
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $categoria = $_POST['categoria'];

    $stmt = $conn->prepare("UPDATE receitas SET titulo = ?, descricao = ?, categoria = ? WHERE id = ?");
    $stmt->bind_param("sssi", $titulo, $descricao, $categoria, $id);
    if ($stmt->execute()) {
        header("Location: gerenciar_receitas.php?msg=editada");
        exit;
    } else {
        echo "Erro ao atualizar.";
    }
}
?>

<!-- Formulário -->
<?php include '../includes/header.php'; ?>
<main class="container">
<link rel="stylesheet" href="../assets/css/style.css">
    <h2>Editar Receita</h2>
    <form method="POST">
        <label>Título:</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($receita['titulo']) ?>" required>
        <label>Descrição:</label>
        <textarea name="descricao" required><?= htmlspecialchars($receita['descricao']) ?></textarea>
        <label>Categoria:</label>
        <input type="text" name="categoria" value="<?= htmlspecialchars($receita['categoria']) ?>" required>
        <button type="submit">Atualizar</button>
    </form>
</main>
<?php include '../includes/footer.php'; ?>