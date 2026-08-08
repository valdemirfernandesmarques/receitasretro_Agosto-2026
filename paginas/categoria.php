<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/conexao.php';

// Obtém o ID da categoria da URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: ../index.php");
    exit();
}

// Busca o nome da categoria
$stmt_cat = $conn->prepare("SELECT nome FROM categorias WHERE id = ?");
$stmt_cat->bind_param("i", $id);
$stmt_cat->execute();
$res_cat = $stmt_cat->get_result();

if ($res_cat->num_rows === 0) {
    header("Location: ../index.php");
    exit();
}

$categoria = $res_cat->fetch_assoc();
$stmt_cat->close();

// Busca as receitas pertencentes a essa categoria
$stmt_rec = $conn->prepare("SELECT id, titulo, descricao, imagem FROM receitas WHERE categoria_id = ? ORDER BY id DESC");
$stmt_rec->bind_param("i", $id);
$stmt_rec->execute();
$receitas = $stmt_rec->get_result();

include_once('../includes/header.php');
?>

<main class="conteudo-principal">
    <h2>Receitas da Categoria: <?php echo htmlspecialchars($categoria['nome']); ?></h2>

    <div class="lista-receitas">
        <?php if ($receitas->num_rows > 0): ?>
            <?php while ($receita = $receitas->fetch_assoc()): ?>
                <div class="card-receita">
                    <?php 
                    // Tratamento dinâmico para URLs do Cloudinary vs Imagens locais
                    $imagem_src = "../imagens/sem-foto.jpg"; // Imagem padrão
                    
                    if (!empty($receita['imagem'])) {
                        if (strpos($receita['imagem'], 'http') === 0) {
                            // Imagem salva no Cloudinary
                            $imagem_src = $receita['imagem'];
                        } else {
                            // Imagem salva no servidor local
                            $caminho_local = "../" . ltrim(str_replace('../', '', $receita['imagem']), '/');
                            if (file_exists($caminho_local)) {
                                $imagem_src = $caminho_local;
                            }
                        }
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($imagem_src); ?>" alt="<?php echo htmlspecialchars($receita['titulo']); ?>" class="img-receita">
                    
                    <h3><?php echo htmlspecialchars($receita['titulo']); ?></h3>
                    <p><?php echo htmlspecialchars($receita['descricao']); ?></p>
                    <a href="receita.php?id=<?php echo $receita['id']; ?>" class="btn-detalhes">Ver Receita</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhuma receita encontrada para esta categoria.</p>
        <?php endif; ?>
    </div>
</main>

<?php 
$stmt_rec->close();
$conn->close();
include_once('../includes/footer.php'); 
?>