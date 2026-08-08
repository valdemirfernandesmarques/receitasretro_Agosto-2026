<?php
session_start();

// Envia cabeçalho HTTP garantindo que o navegador interprete a página em UTF-8 no Render
header('Content-Type: text/html; charset=utf-8');

// Inclui o arquivo de conexão com o banco de dados.
include_once '../includes/conexao.php';

// Força a conexão MySQL a usar UTF-8
if (isset($conn)) {
    $conn->set_charset("utf8mb4");
}

// Inclui o cabeçalho da página.
include_once '../includes/header.php';

// Função utilitária para garantir que o texto esteja em UTF-8 válido e seguro para o htmlspecialchars
function exibir_texto($texto) {
    if (empty($texto)) return '';
    // Converte de ISO-8859-1 para UTF-8 apenas se o texto não for UTF-8 válido
    if (!mb_check_encoding($texto, 'UTF-8')) {
        $texto = mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');
    }
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// --- Lógica para buscar e exibir receitas por categoria ---
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

        <?php 
        while ($receita = $resultReceitas->fetch_assoc()) { 
        ?>
            <fieldset class="card-receita">
                <legend class="receita-titulo">
                    <?php echo exibir_texto($receita['titulo']); ?>
                </legend>
                
                <p class="autor-receita">
                    Escrito por: <strong><?php echo exibir_texto($receita['autor_nome']); ?></strong><br>
                    Publicado em: <strong><?php echo date('d/m/Y \à\s H:i', strtotime($receita['criado_em'])); ?></strong>
                </p>

                <img src="<?php echo exibir_texto($receita['imagem']); ?>" 
                     alt="Imagem da receita <?php echo exibir_texto($receita['titulo']); ?>">

                <section class="receita-conteudo">
                    
                    <div class="receita-bloco">
                        <h4>Ingredientes</h4>
                        <ul class="lista-ingredientes">
                            <?php
                            $ingredientes = explode("\n", $receita['ingredientes']);
                            foreach ($ingredientes as $item) {
                                echo "<li>" . exibir_texto(trim($item)) . "</li>";
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
                                echo "<li>" . exibir_texto(trim($passo)) . "</li>";
                            }
                            ?>
                        </ol>
                    </div>
                </section>

            </fieldset>
        <?php } // Fim do loop while ?>

    </div>

</main>
    
</body>
<?php
include_once('../includes/footer.php');
?>