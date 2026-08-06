<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

require_once '../includes/conexao.php';

// Configura o charset da conexão no MySQL
if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
}

include_once('../includes/header.php');

// Função para corrigir texto com dupla codificação UTF-8 (ex: Ã´ -> ô, Ãª -> ê)
function corrigir_utf8_duplo($texto) {
    if (empty($texto)) return '';
    
    // Remove resquícios de marcas de checkbox caso existam
    $texto = preg_replace('/(check|uncheck)/i', '', $texto);

    // Se contiver a sequência 'Ã' (indício clássico de double UTF-8)
    if (strpos($texto, 'Ã') !== false || !mb_check_encoding($texto, 'UTF-8')) {
        if (function_exists('utf8_decode')) {
            $texto_decodificado = utf8_decode($texto);
            if (mb_check_encoding($texto_decodificado, 'UTF-8')) {
                $texto = $texto_decodificado;
            }
        }
    }
    
    return $texto;
}

// Check de sessão do Admin
if (!isset($_SESSION["usuario_email"]) || $_SESSION["usuario_email"] !== "admin@retro.com") {
    header("Location: ../paginas/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p style='padding:20px; text-align:center;'>ID da receita não especificado ou inválido.</p>";
    include_once('../includes/footer.php');
    exit();
}

$receita_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT r.*, u.nome AS autor_nome, u.email AS autor_email 
                        FROM receitas r 
                        JOIN usuarios u ON r.usuario_id = u.id 
                        WHERE r.id = ? AND r.status = 'pendente'");
$stmt->bind_param("i", $receita_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='padding:20px; text-align:center;'>Receita pendente não encontrada ou já foi liberada/recusada.</p>";
    include_once('../includes/footer.php');
    exit();
}

$receita = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Receita Pendente - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <link rel="stylesheet" href="../assets/css/ver_receita_pendente.css"> 
</head>
<body class="pagina-ver-receita-admin">
    <div class="container-receita-pendente">
        <h2>Visualizar Receita: <?php echo corrigir_utf8_duplo($receita['titulo']); ?></h2>

        <div class="receita-detalhe">
            <p><strong>Autor:</strong> <?php echo corrigir_utf8_duplo($receita['autor_nome']); ?> (<?php echo $receita['autor_email']; ?>)</p>
            <p><strong>Enviado em:</strong> <?php echo date('d/m/Y \à\s H:i', strtotime($receita['criado_em'])); ?></p>
            <p><strong>Status Atual:</strong> <span style="color: orange; font-weight: bold;"><?php echo ucfirst($receita['status']); ?></span></p>

            <?php if ($receita['imagem']): ?>
                <?php
                $caminho_imagem_do_banco = $receita['imagem'];
                $caminho_final_exibicao = '';

                if (strpos($caminho_imagem_do_banco, '../uploads/') === 0) {
                    $caminho_final_exibicao = $caminho_imagem_do_banco;
                } else if (strpos($caminho_imagem_do_banco, 'uploads/') === 0) {
                    $caminho_final_exibicao = '../' . $caminho_imagem_do_banco;
                } else {
                    $caminho_final_exibicao = $caminho_imagem_do_banco; 
                }
                ?>
                <img src="<?php echo $caminho_final_exibicao; ?>" alt="Imagem da Receita" style="max-width: 400px; height: auto;">
            <?php else: ?>
                <p>Nenhuma imagem disponível para esta receita.</p>
            <?php endif; ?>

            <h4>Ingredientes:</h4>
            <ul>
                <?php
                $ingredientes_limpos = corrigir_utf8_duplo($receita['ingredientes']);
                $ingredientes = explode("\n", str_replace("\r", "", $ingredientes_limpos));
                foreach ($ingredientes as $item) {
                    $item_trimado = trim($item);
                    if (!empty($item_trimado)) {
                        echo "<li>" . $item_trimado . "</li>";
                    }
                }
                ?>
            </ul>

            <h4>Modo de Preparo:</h4>
            <ol>
                <?php
                $modo_preparo_limpo = corrigir_utf8_duplo($receita['modo_preparo']);
                $modo_preparo = explode("\n", str_replace("\r", "", $modo_preparo_limpo));
                foreach ($modo_preparo as $passo) {
                    $passo_trimado = trim($passo);
                    if (!empty($passo_trimado)) {
                        echo "<li>" . $passo_trimado . "</li>";
                    }
                }
                ?>
            </ol>
        </div>

        <div class="acoes-admin">
            <form method="post" action="processar_receita.php" style="display: inline-block; margin-right: 10px;">
                <input type="hidden" name="receita_id" value="<?php echo $receita['id']; ?>">
                <button type="submit" name="acao" value="liberar" class="btn-liberar">Liberar Receita</button>
            </form>

            <form method="post" action="processar_receita.php" style="display: inline-block;">
                <input type="hidden" name="receita_id" value="<?php echo $receita['id']; ?>">
                <button type="submit" name="acao" value="recusar" class="btn-recusar" onclick="return confirm('Tem certeza que deseja recusar/excluir esta receita? Esta ação NÃO PODE ser desfeita e a imagem será APAGADA.');">Recusar/Excluir Receita</button>
            </form>
            
            <a href="dashboard.php" class="btn-voltar">Voltar ao Painel</a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
include_once('../includes/footer.php');
?>