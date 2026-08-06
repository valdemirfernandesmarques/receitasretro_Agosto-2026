<?php
session_start(); // Inicia a sessão para acessar variáveis de sessão, como o status do usuário.
require_once '../includes/conexao.php'; // Inclui o arquivo de conexão com o banco de dados.

// Configura o charset do banco para UTF-8
if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
}

include_once('../includes/header.php'); // Inclui o cabeçalho padrão do site.

// --- Funções Auxiliares de Limpeza e Formatação ---
function limpar_texto_utf8($texto) {
    if (empty($texto)) return '';
    if (!mb_check_encoding($texto, 'UTF-8') || preg_match('/[\x80-\xFF]/', $texto)) {
        if (function_exists('mb_convert_encoding')) {
            $texto = mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');
        } elseif (function_exists('utf8_encode')) {
            $texto = utf8_encode($texto);
        }
    }
    return $texto;
}

function limpar_sujeira_texto($texto) {
    $texto_limpo = limpar_texto_utf8($texto);
    // Remove os marcadores indesejados 'uncheck', 'checkuncheck', 'check'
    return preg_replace('/(uncheck|checkuncheck|check)/i', '', $texto_limpo);
}

// --- Verificação de Acesso do Administrador ---
if (!isset($_SESSION["usuario_email"]) || $_SESSION["usuario_email"] !== "admin@retro.com") {
    header("Location: ../paginas/login.php");
    exit();
}

// --- Verificação do ID da Receita na URL ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p style='padding:20px; text-align:center;'>ID da receita não especificado ou inválido.</p>";
    include_once('../includes/footer.php');
    exit();
}

$receita_id = intval($_GET['id']);

// --- Busca dos Detalhes da Receita Pendente ---
$stmt = $conn->prepare("SELECT r.*, u.nome AS autor_nome, u.email AS autor_email 
                        FROM receitas r 
                        JOIN usuarios u ON r.usuario_id = u.id 
                        WHERE r.id = ? AND r.status = 'pendente'");
$stmt->bind_param("i", $receita_id);
$stmt->execute();
$result = $stmt->get_result();

// --- Verificação se a Receita Foi Encontrada ---
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
        <h2>Visualizar Receita: <?php echo htmlspecialchars(limpar_texto_utf8($receita['titulo']), ENT_QUOTES, 'UTF-8'); ?></h2>

        <div class="receita-detalhe">
            <p><strong>Autor:</strong> <?php echo htmlspecialchars(limpar_texto_utf8($receita['autor_nome']), ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($receita['autor_email'], ENT_QUOTES, 'UTF-8'); ?>)</p>
            <p><strong>Enviado em:</strong> <?php echo date('d/m/Y \à\s H:i', strtotime($receita['criado_em'])); ?></p>
            <p><strong>Status Atual:</strong> <span style="color: orange; font-weight: bold;"><?php echo htmlspecialchars(ucfirst($receita['status']), ENT_QUOTES, 'UTF-8'); ?></span></p>

            <?php if ($receita['imagem']): ?>
                <?php
                $caminho_imagem_do_banco = htmlspecialchars($receita['imagem'], ENT_QUOTES, 'UTF-8');
                $caminho_final_exibicao = '';

                if (strpos($caminho_imagem_do_banco, '../uploads/') === 0) {
                    $caminho_final_exibicao = $caminho_imagem_do_banco;
                } else if (strpos($caminho_imagem_do_banco, 'uploads/') === 0) {
                    $caminho_final_exibicao = '../' . $caminho_imagem_do_banco;
                } else {
                    $caminho_final_exibicao = $caminho_imagem_do_banco; 
                }
                ?>
                <img src="<?php echo $caminho_final_exibicao; ?>" alt="Imagem da Receita <?php echo htmlspecialchars(limpar_texto_utf8($receita['titulo']), ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 400px; height: auto;">
            <?php else: ?>
                <p>Nenhuma imagem disponível para esta receita.</p>
            <?php endif; ?>

            <h4>Ingredientes:</h4>
            <ul>
                <?php
                $ingredientes_tratos = limpar_sujeira_texto($receita['ingredientes']);
                $ingredientes = explode("\n", $ingredientes_tratos);
                foreach ($ingredientes as $item) {
                    $item_trimado = trim($item);
                    if (!empty($item_trimado)) {
                        echo "<li>" . htmlspecialchars($item_trimado, ENT_QUOTES, 'UTF-8') . "</li>";
                    }
                }
                ?>
            </ul>

            <h4>Modo de Preparo:</h4>
            <ol>
                <?php
                $modo_preparo_trato = limpar_sujeira_texto($receita['modo_preparo']);
                $modo_preparo = explode("\n", $modo_preparo_trato);
                foreach ($modo_preparo as $passo) {
                    $passo_trimado = trim($passo);
                    if (!empty($passo_trimado)) {
                        echo "<li>" . htmlspecialchars($passo_trimado, ENT_QUOTES, 'UTF-8') . "</li>";
                    }
                }
                ?>
            </ol>
        </div>

        <div class="acoes-admin">
            <form method="post" action="processar_receita.php" style="display: inline-block; margin-right: 10px;">
                <input type="hidden" name="receita_id" value="<?php echo htmlspecialchars($receita['id'], ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" name="acao" value="liberar" class="btn-liberar">Liberar Receita</button>
            </form>

            <form method="post" action="processar_receita.php" style="display: inline-block;">
                <input type="hidden" name="receita_id" value="<?php echo htmlspecialchars($receita['id'], ENT_QUOTES, 'UTF-8'); ?>">
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