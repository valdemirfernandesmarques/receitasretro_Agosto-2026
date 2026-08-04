<?php
// admin/excluir_usuario.php
require_once '../includes/db_connect.php';

if (!isset($_GET['id'])) {
    echo "ID não informado.";
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: gerenciar_usuarios.php?msg=excluido");
exit;