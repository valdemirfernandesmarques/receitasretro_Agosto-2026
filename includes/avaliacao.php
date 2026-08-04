<?php
// includes/avaliacao.php

require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['erro' => 'Login necessário']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$receita_id = $_POST['receita_id'] ?? null;
$estrelas = $_POST['estrelas'] ?? 0;

if ($receita_id && $estrelas > 0 && $estrelas <= 5) {
    $stmt = $pdo->prepare("REPLACE INTO avaliacoes (receita_id, usuario_id, estrelas) VALUES (?, ?, ?)");
    if ($stmt->execute([$receita_id, $usuario_id, $estrelas])) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['erro' => 'Erro ao salvar avaliação']);
    }
} else {
    echo json_encode(['erro' => 'Dados inválidos']);
}