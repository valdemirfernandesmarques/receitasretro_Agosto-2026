<?php
session_start();
// Garante o cabeçalho HTTP em UTF-8
header('Content-Type: text/html; charset=utf-8');

include_once '../includes/conexao.php';

// Garante que a conexão MySQL utilize UTF-8
if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
}

include_once '../includes/header.php';

// --- Lógica para buscar e exibir receitas por categoria ---
if (isset($_GET['cat'])) {
    $categoria = $_GET['cat'];

    // Prepara a consulta para buscar a categoria
    $stmtCategoria = $conn->prepare("SELECT id FROM categorias WHERE nome = ?");
    $stmtCategoria->bind_param("s", $categoria);
    $stmtCategoria->execute();
    $resultCategoria = $stmtCategoria->get_result();

    if ($resultCategoria->num_rows > 0) {
        $categoriaRow = $resultCategoria->fetch_assoc();
        $categoriaId = $categoriaRow['id'];

        // Consulta receitas liberadas
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
    
    <h2 class="titulo-categoria">Receitas: <?php echo htmlspecialchars(ucfirst($categoria), ENT_QUOTES, 'UTF-8'); ?></h2>

    <div class="grid-receitas">

        <?php 
        while ($receita = $resultReceitas->fetch_assoc()) { 
        ?>
            <fieldset class="card-receita">
                <legend class="receita-titulo">
                    <?php echo htmlspecialchars($receita['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                </legend>
                
                <p class="autor-receita">
                    Escrito por: <strong><?php echo htmlspecialchars($receita['autor_nome'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                    Publicado em: <strong><?php echo date('d/m/Y \à\s H:i', strtotime($receita['criado_em'])); ?></strong>
                </p>

                <img src="<?php echo htmlspecialchars($receita['imagem'], ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="Imagem da receita <?php echo htmlspecialchars($receita['titulo'], ENT_QUOTES, 'UTF-8'); ?>">

                <section class="receita-conteudo">
                    
                    <div class="receita-bloco">
                        <h4>Ingredientes</h4>
                        <ul class="lista-ingredientes">
                            <?php
                            $ingredientes = explode("\n", $receita['ingredientes']);
                            foreach ($ingredientes as $item) {
                                $item_limpo = trim($item);
                                if (!empty($item_limpo)) {
                                    echo "<li>" . htmlspecialchars($item_limpo, ENT_QUOTES, 'UTF-8') . "</li>";
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
                                    echo "<li>" . htmlspecialchars($passo_limpo, ENT_QUOTES, 'UTF-8') . "</li>";
                                }
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