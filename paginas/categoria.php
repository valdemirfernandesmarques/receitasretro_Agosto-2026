<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

require_once '../includes/conexao.php';
include_once('../includes/header.php');

/**
 * Função auxiliar para garantir UTF-8 e evitar ataques XSS
 */
function sanitizar_utf8($string) {
    if ($string === null) {
        return '';
    }
    if (!mb_check_encoding($string, 'UTF-8')) {
        $string = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Captura o ID vindo da URL (ex: categoria.php?id=1)
$categoria_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$categoria = null;
$receitas = [];
$todas_categorias = [];

if ($categoria_id) {
    // 1. Busca os dados da categoria selecionada
    $stmtCat = $conn->prepare("SELECT * FROM categorias WHERE id = ?");
    if ($stmtCat) {
        $stmtCat->bind_param("i", $categoria_id);
        $stmtCat->execute();
        $resCat = $stmtCat->get_result();
        $categoria = $resCat->fetch_assoc();
        $stmtCat->close();
    }

    // 2. Busca as receitas cadastradas nessa categoria
    if ($categoria) {
        $stmtRec = $conn->prepare("SELECT * FROM receitas WHERE categoria_id = ? ORDER BY id DESC");
        if ($stmtRec) {
            $stmtRec->bind_param("i", $categoria_id);
            $stmtRec->execute();
            $resRec = $stmtRec->get_result();
            while ($row = $resRec->fetch_assoc()) {
                $receitas[] = $row;
            }
            $stmtRec->close();
        }
    }
}

// Se nenhuma categoria foi selecionada via ID, carrega todas as categorias para a vitrine
if (!$categoria) {
    $resTodas = $conn->query("SELECT * FROM categorias ORDER BY id ASC");
    if ($resTodas) {
        while ($catRow = $resTodas->fetch_assoc()) {
            $todas_categorias[] = $catRow;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $categoria ? sanitizar_utf8($categoria['nome']) : 'Categorias de Receitas'; ?></title>
</head>
<body class="pagina-categoria">
    <main style="max-width: 1200px; margin: 20px auto; padding: 0 15px;">

        <?php if ($categoria): ?>
            <!-- EXIBIÇÃO DA CATEGORIA SELECIONADA -->
            <h1>Receitas de <?php echo sanitizar_utf8($categoria['nome']); ?></h1>

            <?php if (!empty($categoria['descricao'])): ?>
                <p><?php echo sanitizar_utf8($categoria['descricao']); ?></p>
            <?php endif; ?>

            <br>

            <?php if (count($receitas) > 0): ?>
                <div class="lista-receitas" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                    <?php foreach ($receitas as $receita): ?>
                        <div class="card-receita" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                            <?php if (!empty($receita['imagem'])): ?>
                                <img src="<?php echo sanitizar_utf8($receita['imagem']); ?>" alt="<?php echo sanitizar_utf8($receita['titulo']); ?>" style="width: 100%; height: 180px; object-fit: cover; border-radius: 5px;">
                            <?php endif; ?>
                            <h3><?php echo sanitizar_utf8($receita['titulo']); ?></h3>
                            <p><strong>Ingredientes:</strong><br><?php echo nl2br(sanitizar_utf8($receita['ingredientes'])); ?></p>
                            <p><strong>Modo de Preparo:</strong><br><?php echo nl2br(sanitizar_utf8($receita['modo_preparo'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Nenhuma receita cadastrada para esta categoria ainda.</p>
            <?php endif; ?>

            <p style="margin-top: 30px;">
                <a href="categoria.php">&larr; Voltar para todas as categorias</a>
            </p>

        <?php else: ?>
            <!-- VITRINE DE TODAS AS CATEGORIAS (Quando acessa sem ?id=) -->
            <h1>Categorias de Receitas</h1>
            <p>Selecione uma categoria abaixo para visualizar as receitas:</p>
            <br>

            <div class="grid-categorias" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                <?php foreach ($todas_categorias as $cat): ?>
                    <a href="categoria.php?id=<?php echo $cat['id']; ?>" style="display: block; padding: 20px; background: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #333; font-weight: bold; text-align: center;">
                        <?php echo sanitizar_utf8($cat['nome']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
</body>
</html>

<?php include_once('../includes/footer.php'); ?>