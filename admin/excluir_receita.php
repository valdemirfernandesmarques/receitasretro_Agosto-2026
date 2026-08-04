<?php
// admin/excluir_receita.php
require_once '../includes/db_connect.php';

if (!isset($_GET['id'])) {
    echo "ID da receita não informado.";
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM receitas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: gerenciar_receitas.php?msg=excluida");
exit;