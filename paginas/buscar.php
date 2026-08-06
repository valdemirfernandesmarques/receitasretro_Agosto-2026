<?php
// Garante o início da sessão antes de qualquer saída de código
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

require_once "../includes/conexao.php";
require_once "../includes/header.php";

// Função para tratar texto com dupla codificação UTF-8 do banco
function tratar_utf8($texto) {
    if (empty($texto)) return '';
    // Detecta se a string está duplamente codificada e corrige
    if (!mb_check_encoding($texto, 'UTF-8') || preg_match('/[\x80-\xFF]/', $texto)) {
        if (function_exists('mb_convert_encoding')) {
            $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        } elseif (function_exists('utf8_decode')) {
            $texto = utf8_decode($texto);
        }
    }
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}
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
        echo "<h2>🔎 Resultados para: <em>" . tratar_utf8($busca) . "</em></h2>";
        echo "<p class='mensagem-alerta'>❌ Nenhuma receita encontrada.</p>";
    } else {
        echo "<h2>🔎 Resultados para: <em>" . tratar_utf8($busca) . "</em></h2>";
        echo "<div class='tabela-responsive'>";
        echo "<table border='1' cellpadding='10'>";
        echo "<thead><tr><th>Título</th><th>Ingredientes</th><th>Categoria</th><th>Autor</th><th>Data</th></tr></thead>";
        echo "<tbody>";

        while ($row = $resultado->fetch_assoc()) {
            $titulo = tratar_utf8($row['titulo']);
            $ingredientes = nl2br(tratar_utf8($row['ingredientes']));
            $categoria_nome = tratar_utf8($row['categoria_nome'] ?? 'Desconhecida');
            $autor_nome = tratar_utf8($row['autor_nome'] ?? 'Desconhecido');
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