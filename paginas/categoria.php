<?php
header('Content-Type: text/html; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../includes/conexao.php';
include_once '../includes/header.php';

// Função de segurança/fallback para checar e formatar o caminho da imagem
function obter_caminho_imagem($caminho_banco) {
    if (empty($caminho_banco)) {
        return '../assets/img/sem-foto.jpg'; // Imagem padrão caso não exista
    }

    // Normaliza o caminho para exibição na pasta atual
    $caminho_normalizado = $caminho_banco;
    if (strpos($caminho_banco, '../') === 0) {
        $caminho_normalizado = $caminho_banco;
    } else if (strpos($caminho_banco, 'uploads/') === 0) {
        $caminho_normalizado = '../' . $caminho_banco;
    }

    // Se o Render tiver limpado o arquivo do disco, retorna imagem fallback
    if (!file_exists($caminho_normalizado)) {
        return 'https://via.placeholder.com/400x300?text=Imagem+Indispon%C3%ADvel';
    }

    return $caminho_normalizado;
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
            $titulo = function_exists('corrigir_texto') ? corrigir_texto($receita['titulo']) : $receita['titulo'];
            $autor  = function_exists('corrigir_texto') ? corrigir_texto($receita['autor_nome']) : $receita['autor_nome'];
            $ingredientes_raw = function_exists('corrigir_texto') ? corrigir_texto($receita['ingredientes']) : $receita['ingredientes'];
            $preparo_raw      = function_exists('corrigir_texto') ? corrigir_texto($receita['modo_preparo']) : $receita['modo_preparo'];
            
            $src_imagem = obter_caminho_imagem($receita['imagem']);
        ?>
            <fieldset class="card-receita">
                <legend class="receita-titulo">
                    <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>
                </legend>
                
                <p class="autor-receita">
                    Escrito por: <strong><?php echo htmlspecialchars($autor, ENT_QUOTES, 'UTF-8'); ?></strong><br>
                    Publicado em: <strong><?php echo date('d/m/Y \à\s H:i', strtotime($receita['criado_em'])); ?></strong>
                </p>

                <img src="<?php echo htmlspecialchars($src_imagem, ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="Imagem da receita <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>"
                     style="max-width: 100%; height: auto; object-fit: cover;">

                <section class="receita-conteudo">
                    
                    <div class="receita-bloco">
                        <h4>Ingredientes</h4>
                        <ul class="lista-ingredientes">
                            <?php
                            $ingredientes = explode("\n", str_replace("\r", "", $ingredientes_raw));
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
                            $preparo = explode("\n", str_replace("\r", "", $preparo_raw));
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