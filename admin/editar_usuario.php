<?php
// admin/editar_usuario.php
require_once '../includes/db_connect.php';

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    echo "ID do usuário não especificado.";
    exit;
}

$id = $_GET['id'];
$usuario = null;

// Busca o usuário no banco
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
} else {
    echo "Usuário não encontrado.";
    exit;
}

// Atualiza os dados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];

    $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, telefone = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nome, $email, $telefone, $id);
    if ($stmt->execute()) {
        header("Location: gerenciar_usuarios.php?msg=editado");
        exit;
    } else {
        echo "Erro ao atualizar.";
    }
}
?>

<!-- Formulário de edição -->
<?php include '../includes/header.php'; ?>
<main class="container">
<link rel="stylesheet" href="../assets/css/style.css">
    <h2>Editar Usuário</h2>
    <form method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        <label>Telefone:</label>
        <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>">
        <button type="submit">Salvar</button>
    </form>
</main>
<?php include '../includes/footer.php'; ?>