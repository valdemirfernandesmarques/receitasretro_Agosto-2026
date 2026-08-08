<?php
// Inicia a sessão (se ainda não estiver iniciada), embora não seja diretamente usada aqui, é uma boa prática
session_start();

// Inclui o arquivo de conexão com o banco de dados.
// É essencial para estabelecer a comunicação com o MySQL.
include_once '../includes/conexao.php';

// Inclui o cabeçalho da página (contém tags <head>, abertura do <body>, etc.).
// Isso ajuda a manter um layout consistente em todo o site.
include_once '../includes/header.php';

// --- Lógica para buscar e exibir receitas por categoria ---
// Verifica se o parâmetro 'cat' (categoria) foi passado na URL via método GET.
// Isso indica que o usuário está tentando visualizar receitas de uma categoria específica.
if (isset($_GET['cat'])) {
    // Pega o nome da categoria da URL e armazena na variável $categoria.
    $categoria = $_GET['cat'];

    // Prepara uma consulta SQL para obter o ID da categoria com base no nome.
    // Usar prepared statements previne ataques de SQL Injection.
    $stmtCategoria = $conn->prepare("SELECT id FROM categorias WHERE nome = ?");
    // 's' indica que o parâmetro é uma string.
    $stmtCategoria->bind_param("s", $categoria);
    // Executa a consulta preparada.
    $stmtCategoria->execute();
    // Obtém o resultado da consulta.
    $resultCategoria = $stmtCategoria->get_result();

    // Verifica se alguma categoria foi encontrada com o nome fornecido.
    if ($resultCategoria->num_rows > 0) {
        // Se a categoria existe, obtém a linha de resultado (que contém o ID da categoria).
        $categoriaRow = $resultCategoria->fetch_assoc();
        // Armazena o ID da categoria.
        $categoriaId = $categoriaRow['id'];

        // --- CORREÇÃO IMPORTANTE APLICADA AQUI ---
        // Prepara a consulta para buscar as receitas.
        // Adiciona um JOIN com a tabela 'usuarios' para obter o nome do autor da receita.
        // A CLÁUSULA 'AND r.status = 'liberado'' GARANTE QUE SOMENTE RECEITAS
        // COM O STATUS 'liberado' SERÃO EXIBIDAS.
        // Receitas com 'pendente' ou qualquer outro status não aparecerão para o usuário final.
        $stmtReceitas = $conn->prepare("SELECT r.*, u.nome AS autor_nome 
                                         FROM receitas r 
                                         JOIN usuarios u ON r.usuario_id = u.id 
                                         WHERE r.categoria_id = ? AND r.status = 'liberado'");
        // 'i' indica que o parâmetro é um inteiro (o ID da categoria).
        $stmtReceitas->bind_param("i", $categoriaId);
        // Executa a consulta de receitas.
        $stmtReceitas->execute();
        // Obtém o resultado da consulta de receitas.
        $resultReceitas = $stmtReceitas->get_result();

    } else {
        // Se nenhuma categoria for encontrada com o nome especificado.
        echo "<p style='padding:20px;'>Categoria não encontrada.</p>";
        // Termina a execução do script para evitar processamento desnecessário.
        exit;
    }
} else {
    // Se o parâmetro 'cat' não foi passado na URL (URL incompleta).
    echo "<p style='padding:20px;'>Categoria não especificada.</p>";
    // Termina a execução do script.
    exit;
}
?>

<body>
<main class="container pagina-categoria">
    
    <h2 class="titulo-categoria">Receitas: <?php echo htmlspecialchars(ucfirst($categoria)); ?></h2>

    <div class="grid-receitas">

        <?php 
        // Loop que itera sobre cada receita encontrada na consulta.
        // fetch_assoc() busca a próxima linha do conjunto de resultados como um array associativo.
        while ($receita = $resultReceitas->fetch_assoc()) { 
        ?>
            <fieldset class="card-receita">
                <legend class="receita-titulo">
                    <?php 
                    // Exibe o título da receita, usando htmlspecialchars para prevenir XSS.
                    echo htmlspecialchars($receita['titulo']); 
                    ?>
                </legend>
                
                <p class="autor-receita">
                    Escrito por: <strong><?php echo htmlspecialchars($receita['autor_nome']); ?></strong><br>
                    Publicado em: <strong><?php echo date('d/m/Y \à\s H:i', strtotime($receita['criado_em'])); ?></strong>
                </p>

                <img src="<?php echo htmlspecialchars($receita['imagem']); ?>" 
                     alt="Imagem da receita <?php echo htmlspecialchars($receita['titulo']); ?>">

                <section class="receita-conteudo">
                    
                    <div class="receita-bloco">
                        <h4>Ingredientes</h4>
                        <ul class="lista-ingredientes">
                            <?php
                            // Divide a string de ingredientes (armazenada com quebras de linha) em um array.
                            $ingredientes = explode("\n", $receita['ingredientes']);
                            // Itera sobre cada item do array de ingredientes e o exibe como um item de lista.
                            foreach ($ingredientes as $item) {
                                // trim() remove espaços em branco extras, htmlspecialchars previne XSS.
                                echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
                            }
                            ?>
                        </ul>
                    </div>

                    <div class="receita-bloco">
                        <h4>Modo de Preparo</h4>
                        <ol class="lista-preparo">
                            <?php
                            // Divide a string do modo de preparo em um array.
                            $preparo = explode("\n", $receita['modo_preparo']);
                            // Itera sobre cada passo do preparo e o exibe como um item de lista ordenada.
                            foreach ($preparo as $passo) {
                                // trim() remove espaços em branco extras, htmlspecialchars previne XSS.
                                echo "<li>" . htmlspecialchars(trim($passo)) . "</li>";
                            }
                            ?>
                        </ol>
                    </div>
                </section>

                </fieldset>
        <?php } // Fim do loop while ?>

    </div>
 <!------------------------------------------------------------------------->
<!-- script global do Google AdSense(para ganhar com propagandas: blocos)-->
     
<!------------------------------------------------------------------------->
    
</main>
    
</body>
<?php
// Inclui o rodapé da página (encerramento do body e html).
// Ajuda a manter um layout consistente.
include_once('../includes/footer.php');
?>
