<?php
header('Content-Type: text/html; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../includes/conexao.php';
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
                                         WHERE r.categoria_id = ? AND r.status = 'liberado'");
        $stmtReceitas->bind_param("i", $categoriaId);
        $stmtReceitas->execute();
        $resultReceitas = $stmtReceitas->get_result();

    } else {
        echo "<p style='padding:20px;'>Categoria não encontrada.</p>";
        include_once '../includes/footer.php';
        exit;
    }
} else {
    echo "<p style='padding:20px;'>Categoria não especificada.</p>";
    include_once '../includes/footer.php';
    exit;
}
?>

<body>
<main class="container pagina-categoria">
    
    <h2 class="titulo-categoria">Receitas: <?php echo htmlspecialchars(ucfirst($categoria), ENT_QUOTES, 'UTF-8'); ?></h2>

    <div class="grid-receitas">

        <?php 
        while ($receita = $resultReceitas->fetch_assoc()) { 
            // Aplica a correção de codificação nos dados vindos do banco
            $titulo = corrigir_texto($receita['titulo']);
            $autor  = corrigir_texto($receita['autor_nome']);
            $ingredientes_raw = corrigir_texto($receita['ingredientes']);
            $preparo_raw      = corrigir_texto($receita['modo_preparo']);
        ?>
            <fieldset class="card-receita">
                <legend class="receita-titulo">
                    <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>
                </legend>
                
                <p class="autor-receita">
                    Escrito por: <strong><?php echo htmlspecialchars($autor, ENT_QUOTES, 'UTF-8'); ?></strong><br>
                    Publicado em: <strong><?php echo date('d/m/Y \à\s H:i', strtotime($receita['criado_em'])); ?></strong>
                </p>

                <img src="<?php echo htmlspecialchars($receita['imagem'], ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="Imagem da receita <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>">

                <section class="receita-conteudo">
                    
                    <div class="receita-bloco">
                        <h4>Ingredientes</h4>
                        <ul class="lista-ingredientes">
                            <?php
                            $ingredientes = explode("\n", $ingredientes_raw);
                            foreach ($ingredientes as $item) {
                                $itemLimpo = trim($item);
                                if (!empty($itemLimpo)) {
                                    echo "<li>" . htmlspecialchars($itemLimpo, ENT_QUOTES, 'UTF-8') . "</li>";
                                }
                            }
                            ?>
                        </ul>
                    </div>

                    <div class="receita-bloco">
                        <h4>Modo de Preparo</h4>
                        <ol class="lista-preparo">
                            <?php
                            $preparo = explode("\n", $preparo_raw);
                            foreach ($preparo as $passo) {
                                $passoLimpo = trim($passo);
                                if (!empty($passoLimpo)) {
                                    echo "<li>" . htmlspecialchars($passoLimpo, ENT_QUOTES, 'UTF-8') . "</li>";
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
include_once '../includes/footer.php';
?>