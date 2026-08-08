<?php
session_start();
include_once '../includes/conexao.php';
include_once '../includes/header.php';

// Função para exibir o texto com segurança e decodificar entidades HTML antigas
function exibir_texto($texto) {
    if (empty($texto)) return '';
    // Decodifica entidades HTML que foram gravadas no banco anteriormente
    $texto_decodificado = htmlspecialchars_decode($texto, ENT_QUOTES);
    // Aplica a sanitização para exibição limpa em UTF-8
    return htmlspecialchars($texto_decodificado, ENT_QUOTES, 'UTF-8');
}

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
    
    <h2 class="titulo-categoria">Receitas: <?php echo exibir_texto(ucfirst($categoria)); ?></h2>

    <div class="grid-receitas">

        <?php while ($receita = $resultReceitas->fetch_assoc()) { ?>
            <fieldset class="card-receita">
                <legend class="receita-titulo">
                    <?php echo exibir_texto($receita['titulo']); ?>
                </legend>
                
                <p class="autor-receita">
                    Escrito por: <strong><?php echo exibir_texto($receita['autor_nome']); ?></strong><br>
                    Publicado em: <strong><?php echo date('d/m/Y \à\s H:i', strtotime($receita['criado_em'])); ?></strong>
                </p>

                <img src="<?php echo htmlspecialchars($receita['imagem'], ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="Imagem da receita <?php echo exibir_texto($receita['titulo']); ?>">

                <section class="receita-conteudo">
                    
                    <div class="receita-bloco">
                        <h4>Ingredientes</h4>
                        <ul class="lista-ingredientes">
                            <?php
                            $ingredientes = explode("\n", $receita['ingredientes']);
                            foreach ($ingredientes as $item) {
                                $item_limpo = trim($item);
                                if ($item_limpo !== '') {
                                    echo "<li>" . exibir_texto($item_limpo) . "</li>";
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
                                if ($passo_limpo !== '') {
                                    echo "<li>" . exibir_texto($passo_limpo) . "</li>";
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
<?php include_once('../includes/footer.php'); ?>