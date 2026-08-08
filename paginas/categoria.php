<?php
session_start();

// Cabeçalho HTTP forçando UTF-8
header('Content-Type: text/html; charset=utf-8');

// Inclui a conexão
include_once '../includes/conexao.php';

if (isset($conn)) {
    $conn->set_charset("utf8mb4");
}

include_once '../includes/header.php';

// Função inteligente para corrigir tanto o texto antigo (Mojibake/Ã§) quanto o texto novo
function corrigir_dupla_codificacao($texto) {
    if (empty($texto)) return '';
    
    // Se contiver sequências típicas de Mojibake (UTF-8 lido como Latin1)
    if (preg_match('/[\xC2\xC3][\x80-\xBF]/', $texto)) {
        $texto = utf8_decode($texto);
    }
    
    // Garante validação UTF-8 limpa
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// --- Lógica de busca ---
if (isset($_GET['cat'])) {
    $categoria = $_GET['cat'];

    $stmtCategoria = $conn->prepare("SELECT id FROM categorias WHERE nome = ?");
    $stmtCategoria->bind_param("s", $categoria);
    $stmtCategoria->execute();
    $resultCategoria = $stmtCategoria->get_result();

    if ($resultCategoria->num_rows > 0) {
        $categoriaRow = $resultCategoria->fetch_assoc();
        $categoriaId = $categoriaRow['id'];

        $stmtReceitas = $conn->prepare("SELECT r.*, u.nome AS autor_nome 
                                         FROM receitas r 
                                         JOIN usuarios u ON r.usuario_id = u.id 
                                         WHERE r.categoria_id = ? AND r.status = 'liberado'");
        $stmtReceitas->bind_param("i", $categoriaId);
        $stmtReceitas->execute();
        $resultReceitas = $stmtReceitas->get_result();

    } else {
        echo "<p style='padding:20px;'>Categoria não encontrada.</p>";
        exit;
    }
} else {
        echo "<p style='padding:20px;'>Categoria não especificada.</p>";
        exit;
}
?>

<body>
<main class="container pagina-categoria">
    
    <h2 class="titulo-categoria">Receitas: <?php echo corrigir_dupla_codificacao(ucfirst($categoria)); ?></h2>

    <div class="grid-receitas">

        <?php 
        while ($receita = $resultReceitas->fetch_assoc()) { 
        ?>
            <fieldset class="card-receita">
                <legend class="receita-titulo">
                    <?php echo corrigir_dupla_codificacao($receita['titulo']); ?>
                </legend>
                
                <p class="autor-receita">
                    Escrito por: <strong><?php echo corrigir_dupla_codificacao($receita['autor_nome']); ?></strong><br>
                    Publicado em: <strong><?php echo date('d/m/Y \à\s H:i', strtotime($receita['criado_em'])); ?></strong>
                </p>

                <img src="<?php echo htmlspecialchars($receita['imagem'], ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="Imagem da receita <?php echo corrigir_dupla_codificacao($receita['titulo']); ?>">

                <section class="receita-conteudo">
                    
                    <div class="receita-bloco">
                        <h4>Ingredientes</h4>
                        <ul class="lista-ingredientes">
                            <?php
                            $ingredientes = explode("\n", $receita['ingredientes']);
                            foreach ($ingredientes as $item) {
                                echo "<li>" . corrigir_dupla_codificacao(trim($item)) . "</li>";
                            }
                            ?>
                        </ul>
                    </div>

                    <div class="receita-bloco">
                        <h4>Modo de Preparo</h4>
                        <ol class="lista-preparo">
                            <?php
                            $preparo = explode("\n", $receita['modo_preparo']);
                            foreach ($preparo as $passo) {
                                echo "<li>" . corrigir_dupla_codificacao(trim($passo)) . "</li>";
                            }
                            ?>
                        </ol>
                    </div>
                </section>

            </fieldset>
        <?php } ?>

    </div>

</main>
    
</body>
<?php
include_once('../includes/footer.php');
?>