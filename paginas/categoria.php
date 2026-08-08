<?php
session_start();

header('Content-Type: text/html; charset=utf-8');

include_once '../includes/conexao.php';

if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
}

/**
 * Repara textos antigos com dupla codificação UTF-8 e exibe com segurança.
 */
function exibir_texto_limpo($texto) {
    if (empty($texto)) return '';
    
    // 1. Decodifica entidades HTML antigas (ex: &atilde;, &ccedil;)
    $texto = html_entity_decode($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // 2. Detecta e repara Mojibake/dupla codificação UTF-8 comum nas receitas antigas
    if (preg_match('/[\xC2\xC3][\x80-\xBF]/', $texto)) {
        $texto = utf8_decode($texto);
    }
    
    // 3. Converte para exibição HTML segura
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

include_once '../includes/header.php';

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
                                         WHERE r.categoria_id = ? AND r.status = 'liberado'
                                         ORDER BY r.criado_em DESC");
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
    
    <h2 class="titulo-categoria">Receitas: <?php echo exibir_texto_limpo(ucfirst($categoria)); ?></h2>

    <div class="grid-receitas">

        <?php 
        while ($receita = $resultReceitas->fetch_assoc()) { 
        ?>
            <fieldset class="card-receita">
                <legend class="receita-titulo">
                    <?php echo exibir_texto_limpo($receita['titulo']); ?>
                </legend>
                
                <p class="autor-receita">
                    Escrito por: <strong><?php echo exibir_texto_limpo($receita['autor_nome']); ?></strong><br>
                    Publicado em: <strong><?php echo date('d/m/Y \à\s H:i', strtotime($receita['criado_em'])); ?></strong>
                </p>

                <img src="<?php echo exibir_texto_limpo($receita['imagem']); ?>" 
                     alt="Imagem da receita <?php echo exibir_texto_limpo($receita['titulo']); ?>">

                <section class="receita-conteudo">
                    
                    <div class="receita-bloco">
                        <h4>Ingredientes</h4>
                        <ul class="lista-ingredientes">
                            <?php
                            $ingredientes = explode("\n", $receita['ingredientes']);
                            foreach ($ingredientes as $item) {
                                $item_limpo = trim($item);
                                if (!empty($item_limpo)) {
                                    echo "<li>" . exibir_texto_limpo($item_limpo) . "</li>";
                                }
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
                                $passo_limpo = trim($passo);
                                if (!empty($passo_limpo)) {
                                    echo "<li>" . exibir_texto_limpo($passo_limpo) . "</li>";
                                }
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