<?php
// Inclui o cabeçalho primeiro (ele já cuida de session_start e da inclusão da conexão)
require_once "../includes/conexao.php";

// Garante UTF-8 no banco de dados sem quebrar o servidor
if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
}

require_once "../includes/header.php";
?>

<link rel="stylesheet" href="../assets/css/buscar.css">

<main class="conteudo-principal">
<?php
$busca = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($busca)) {
    echo "<p class='mensagem-alerta'>🔍 Nenhum termo de busca informado.</p>";
} else {
    $busca_escapada = $conn->real_escape_string($busca);

    $sql = "SELECT r.*, u.nome AS autor_nome, c.nome AS categoria_nome
            FROM receitas r
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            LEFT JOIN categorias c ON r.categoria_id = c.id
            WHERE r.titulo LIKE '%$busca_escapada%'
               OR r.ingredientes LIKE '%$busca_escapada%'
               OR c.nome LIKE '%$busca_escapada%'
               OR u.nome LIKE '%$busca_escapada%'";

    $resultado = $conn->query($sql);

    if (!$resultado) {
        echo "<p class='mensagem-erro'>Erro na consulta no banco de dados.</p>";
    } elseif ($resultado->num_rows === 0) {
        echo "<h2>🔎 Resultados para: <em>" . htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') . "</em></h2>";
        echo "<p class='mensagem-alerta'>❌ Nenhuma receita encontrada.</p>";
    } else {
        echo "<h2>🔎 Resultados para: <em>" . htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') . "</em></h2>";
        echo "<div class='tabela-responsive'>";
        echo "<table border='1' cellpadding='10'>";
        echo "<thead><tr><th>Título</th><th>Ingredientes</th><th>Categoria</th><th>Autor</th><th>Data</th></tr></thead>";
        echo "<tbody>";

        while ($row = $resultado->fetch_assoc()) {
            // Decodifica possíveis entidades antigas e força exibição limpa em UTF-8
            $titulo = htmlspecialchars(html_entity_decode($row['titulo'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
            $ingredientes = nl2br(htmlspecialchars(html_entity_decode($row['ingredientes'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
            $categoria_nome = htmlspecialchars(html_entity_decode($row['categoria_nome'] ?? 'Desconhecida', ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
            $autor_nome = htmlspecialchars(html_entity_decode($row['autor_nome'] ?? 'Desconhecido', ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
            $data_formatada = date("d/m/Y H:i", strtotime($row['criado_em']));

            echo "<tr>
                    <td data-label='Título'>$titulo</td>
                    <td data-label='Ingredientes'>$ingredientes</td>
                    <td data-label='Categoria'>$categoria_nome</td>
                    <td data-label='Autor'>$autor_nome</td>
                    <td data-label='Data'>$data_formatada</td>
                  </tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }
}
?>
</main>

<?php require_once "../includes/footer.php"; ?>