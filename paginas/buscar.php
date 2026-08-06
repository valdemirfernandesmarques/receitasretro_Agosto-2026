<?php
// Garante o início da sessão antes de qualquer saída de código
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../includes/conexao.php";

// Configuração UTF-8 na conexão
if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
}

require_once('../includes/header.php');
?>

<link rel="stylesheet" href="../assets/css/buscar.css">

<main class="conteudo-principal">
<?php
$busca = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($busca)) {
    echo "<p class='mensagem-alerta'>🔍 Nenhum termo de busca informado.</p>";
} else {
    $busca_escapada = $conn->real_escape_string($busca);

    // Query com JOIN para buscar também pelo nome do usuário autor e categoria
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
        echo "<p class='mensagem-erro'>Erro na consulta: " . htmlentities($conn->error) . "</p>";
    } elseif ($resultado->num_rows === 0) {
        echo "<h2>🔎 Resultados para: <em>" . htmlentities($busca) . "</em></h2>";
        echo "<p class='mensagem-alerta'>❌ Nenhuma receita encontrada.</p>";
    } else {
        echo "<h2>🔎 Resultados para: <em>" . htmlentities($busca) . "</em></h2>";
        echo "<div class='tabela-responsive'>";
        echo "<table border='1' cellpadding='10'>";
        echo "<thead><tr><th>Título</th><th>Ingredientes</th><th>Categoria</th><th>Autor</th><th>Data</th></tr></thead>";
        echo "<tbody>";

        while ($row = $resultado->fetch_assoc()) {
            $titulo = htmlentities($row['titulo']);
            $ingredientes = nl2br(htmlentities($row['ingredientes']));
            $categoria_nome = htmlentities($row['categoria_nome'] ?? 'Desconhecida');
            $autor_nome = htmlentities($row['autor_nome'] ?? 'Desconhecido');
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

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
</main>

<?php include_once('../includes/footer.php'); ?>