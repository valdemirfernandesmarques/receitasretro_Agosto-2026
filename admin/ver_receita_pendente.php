<?php
session_start(); // Inicia a sessão para acessar variáveis de sessão, como o status do usuário.
require_once '../includes/conexao.php'; // Inclui o arquivo de conexão com o banco de dados.
include_once('../includes/header.php'); // Inclui o cabeçalho padrão do site.

// --- Verificação de Acesso do Administrador ---
// Esta é uma etapa de segurança crucial. Garante que apenas usuários com o email "admin@retro.com"
// (que assumimos ser o administrador) possam acessar esta página.
if (!isset($_SESSION["usuario_email"]) || $_SESSION["usuario_email"] !== "admin@retro.com") {
    // Se o usuário não for o administrador, redireciona para a página de login ou para a página inicial.
    header("Location: ../paginas/login.php");
    exit(); // Encerra a execução do script para prevenir qualquer processamento adicional.
}

// --- Verificação do ID da Receita na URL ---
// Verifica se o parâmetro 'id' foi passado na URL via método GET e se ele é um número válido.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Se o ID não for especificado ou não for um número, exibe uma mensagem de erro.
    echo "<p style='padding:20px; text-align:center;'>ID da receita não especificado ou inválido.</p>";
    include_once('../includes/footer.php'); // Inclui o rodapé antes de encerrar.
    exit(); // Encerra o script.
}

// Converte o ID da receita para um inteiro para maior segurança e consistência.
$receita_id = intval($_GET['id']);

// --- Busca dos Detalhes da Receita Pendente ---
// Prepara uma consulta SQL para obter todos os detalhes da receita,
// incluindo o nome e e-mail do usuário que a enviou.
// A cláusula `WHERE r.status = 'pendente'` é fundamental para garantir
// que o administrador visualize apenas receitas que ainda precisam de aprovação.
$stmt = $conn->prepare("SELECT r.*, u.nome AS autor_nome, u.email AS autor_email 
                        FROM receitas r 
                        JOIN usuarios u ON r.usuario_id = u.id 
                        WHERE r.id = ? AND r.status = 'pendente'");
$stmt->bind_param("i", $receita_id); // 'i' indica que o parâmetro é um inteiro.
$stmt->execute(); // Executa a consulta.
$result = $stmt->get_result(); // Obtém o resultado da consulta.

// --- Verificação se a Receita Foi Encontrada ---
if ($result->num_rows === 0) {
    // Se nenhuma receita correspondente (com o status 'pendente') for encontrada, exibe uma mensagem.
    echo "<p style='padding:20px; text-align:center;'>Receita pendente não encontrada ou já foi liberada/recusada.</p>";
    include_once('../includes/footer.php');
    exit();
}

// Obtém a linha da receita como um array associativo.
$receita = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Receita Pendente - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <link rel="stylesheet" href="../assets/css/ver_receita_pendente.css"> </head>
<body class="pagina-ver-receita-admin">
    <div class="container-receita-pendente">
        <h2>Visualizar Receita: <?php echo htmlspecialchars($receita['titulo']); ?></h2>

        <div class="receita-detalhe">
            <p><strong>Autor:</strong> <?php echo htmlspecialchars($receita['autor_nome']); ?> (<?php echo htmlspecialchars($receita['autor_email']); ?>)</p>
            <p><strong>Enviado em:</strong> <?php echo date('d/m/Y \à\s H:i', strtotime($receita['criado_em'])); ?></p>
            <p><strong>Status Atual:</strong> <span style="color: orange; font-weight: bold;"><?php echo htmlspecialchars(ucfirst($receita['status'])); ?></span></p>

            <?php if ($receita['imagem']): // Verifica se existe um caminho de imagem ?>
                <?php
                // --- INÍCIO DA CORREÇÃO DE CAMINHO DA IMAGEM ---
                // O objetivo é que o caminho do src da tag <img> seja sempre '../uploads/nome_da_imagem.jpg'
                // Esta página está em 'admin/', então precisamos subir um nível para acessar 'uploads/'.

                $caminho_imagem_do_banco = htmlspecialchars($receita['imagem']);
                $caminho_final_exibicao = '';

                // Verifica se o caminho já começa com '../uploads/' (como alguns do seu DB)
                if (strpos($caminho_imagem_do_banco, '../uploads/') === 0) {
                    $caminho_final_exibicao = $caminho_imagem_do_banco; // Já está no formato correto para a tag <img>
                } 
                // Verifica se o caminho começa com 'uploads/' (como outros do seu DB)
                else if (strpos($caminho_imagem_do_banco, 'uploads/') === 0) {
                    $caminho_final_exibicao = '../' . $caminho_imagem_do_banco; // Adiciona o '../' necessário
                } 
                // Caso o caminho não esteja em nenhum dos formatos esperados (poderia ser um fallback ou erro)
                else {
                    // Aqui você pode definir um caminho para uma imagem de placeholder, ou apenas deixar vazio
                    // Por exemplo: $caminho_final_exibicao = '../assets/img/placeholder.png';
                    // Por enquanto, vamos deixá-lo como está, o que pode resultar em imagem quebrada
                    // se o formato for totalmente diferente, mas evita adicionar "../" em excesso.
                    $caminho_final_exibicao = $caminho_imagem_do_banco; 
                }
                // --- FIM DA CORREÇÃO DE CAMINHO DA IMAGEM ---
                ?>
                <img src="<?php echo $caminho_final_exibicao; ?>" alt="Imagem da Receita <?php echo htmlspecialchars($receita['titulo']); ?>" style="max-width: 400px; height: auto;">
            <?php else: ?>
                <p>Nenhuma imagem disponível para esta receita.</p>
            <?php endif; ?>

            <h4>Ingredientes:</h4>
            <ul>
                <?php
                // Divide a string de ingredientes por quebra de linha.
                $ingredientes = explode("\n", $receita['ingredientes']);
                foreach ($ingredientes as $item) {
                    $item_trimado = trim($item); // Remove espaços em branco do início/fim da linha.
                    if (!empty($item_trimado)) { // Garante que apenas linhas com conteúdo sejam exibidas.
                        echo "<li>" . htmlspecialchars($item_trimado) . "</li>"; // Exibe como item de lista.
                    }
                }
                ?>
            </ul>

            <h4>Modo de Preparo:</h4>
            <ol>
                <?php
                // Divide a string do modo de preparo por quebra de linha.
                $modo_preparo = explode("\n", $receita['modo_preparo']);
                foreach ($modo_preparo as $passo) {
                    $passo_trimado = trim($passo); // Remove espaços em branco do início/fim da linha.
                    if (!empty($passo_trimado)) { // Garante que apenas linhas com conteúdo sejam exibidas.
                        echo "<li>" . htmlspecialchars($passo_trimado) . "</li>"; // Exibe como item de lista numerada.
                    }
                }
                ?>
            </ol>
        </div>

        <div class="acoes-admin">
            <form method="post" action="processar_receita.php" style="display: inline-block; margin-right: 10px;">
                <input type="hidden" name="receita_id" value="<?php echo htmlspecialchars($receita['id']); ?>">
                <button type="submit" name="acao" value="liberar" class="btn-liberar">Liberar Receita</button>
            </form>

            <form method="post" action="processar_receita.php" style="display: inline-block;">
                <input type="hidden" name="receita_id" value="<?php echo htmlspecialchars($receita['id']); ?>">
                <button type="submit" name="acao" value="recusar" class="btn-recusar" onclick="return confirm('Tem certeza que deseja recusar/excluir esta receita? Esta ação NÃO PODE ser desfeita e a imagem será APAGADA.');">Recusar/Excluir Receita</button>
            </form>
            
            <a href="dashboard.php" class="btn-voltar">Voltar ao Painel</a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close(); // Fecha o statement preparado.
$conn->close(); // Fecha a conexão com o banco de dados.
include_once('../includes/footer.php'); // Inclui o rodapé padrão do site.
?>