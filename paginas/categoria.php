<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

// Caminho corrigido para a pasta includes/
require_once '../includes/conexao.php';

/**
 * Função para tratar e formatar strings garantindo UTF-8 correto
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

$categoria_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$categoria = null;
$receitas = [];

if ($categoria_id) {
    // Buscar informações da categoria (Usando MySQLi)
    $stmtCat = $conn->prepare("SELECT * FROM categorias WHERE id = ?");
    if ($stmtCat) {
        $stmtCat->bind_param("i", $categoria_id);
        $stmtCat->execute();
        $resCat = $stmtCat->get_result();
        $categoria = $resCat->fetch_assoc();
        $stmtCat->close();
    }

    // Buscar receitas pertencentes a essa categoria (Usando MySQLi)
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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $categoria ? sanitizar_utf8($categoria['nome']) : 'Categoria Não Encontrada'; ?></title>
</head>
<body>

    <?php if ($categoria): ?>
        <h1>Categoria: <?php echo sanitizar_utf8($categoria['nome']); ?></h1>

        <?php if (!empty($categoria['descricao'])): ?>
            <p><?php echo sanitizar_utf8($categoria['descricao']); ?></p>
        <?php endif; ?>

        <h2>Receitas nesta Categoria</h2>

        <?php if (count($receitas) > 0): ?>
            <ul>
                <?php foreach ($receitas as $receita): ?>
                    <li>
                        <h3><?php echo sanitizar_utf8($receita['titulo']); ?></h3>
                        <p><strong>Ingredientes:</strong><br><?php echo nl2br(sanitizar_utf8($receita['ingredientes'])); ?></p>
                        <p><strong>Modo de Preparo:</strong><br><?php echo nl2br(sanitizar_utf8($receita['modo_preparo'])); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Nenhuma receita cadastrada para esta categoria ainda.</p>
        <?php endif; ?>

    <?php else: ?>
        <h1>Categoria não encontrada</h1>
        <p>A categoria solicitada não existe ou o ID informado é inválido.</p>
    <?php endif; ?>

</body>
</html>