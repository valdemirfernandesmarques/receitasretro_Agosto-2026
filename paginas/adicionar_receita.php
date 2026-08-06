<?php
// Toda a lógica PHP de verificação e salvamento deve vir ANTES de qualquer HTML ou include de layout
session_start();
require_once '../includes/conexao.php';

// Garante que a conexão aceite caracteres UTF-8
if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
}

// 1. Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../paginas/cadastro.php?sucesso=1");
    exit();
}

// 2. Verifica se o status do usuário permite adicionar receita
if ($_SESSION["usuario_status"] !== "liberado") {
    echo "<script>alert('Você ainda não tem permissão para adicionar receitas. Aguarde liberação do administrador.'); window.location.href='login.php';</script>";
    exit();
}

// 3. Processamento do formulário de inserção
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo_bruto = $_POST["titulo"];
    $ingredientes_bruto = $_POST["ingredientes"];
    $modo_preparo_bruto = $_POST["modo_preparo"];

    // Normalização das quebras de linha
    $ingredientes_normalizado = str_replace(array("\r\n", "\r"), "\n", $ingredientes_bruto);
    $modo_preparo_normalizado = str_replace(array("\r\n", "\r"), "\n", $modo_preparo_bruto);

    // Remoção de linhas vazias
    $ingredientes_limpo = preg_replace("/\n+/", "\n", $ingredientes_normalizado);
    $modo_preparo_limpo = preg_replace("/\n+/", "\n", $modo_preparo_normalizado);

    // Limpeza de espaços em branco por linha
    $ingredientes_linhas = array_map('trim', explode("\n", $ingredientes_limpo));
    $ingredientes = implode("\n", array_filter($ingredientes_linhas, 'strlen'));

    $modo_preparo_linhas = array_map('trim', explode("\n", $modo_preparo_limpo));
    $modo_preparo = implode("\n", array_filter($modo_preparo_linhas, 'strlen'));

    // Título sem espaços extras nas pontas
    $titulo = trim($titulo_bruto);

    $categoria_id = isset($_POST["categoria_id"]) ? intval($_POST["categoria_id"]) : null;
    $usuario_id = $_SESSION["usuario_id"];
    $imagem_caminho = null;

    // Upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $nomeOriginal = basename($_FILES['imagem']['name']);
        $tipo = $_FILES['imagem']['type'];
        $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);

        $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array(strtolower($extensao), $tiposPermitidos)) {
            die("Tipo de arquivo não permitido.");
        }

        $nomeArquivo = uniqid("img_", true) . "." . $extensao;
        $caminho = "../uploads/" . $nomeArquivo;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
            $stmt_img = $conn->prepare("INSERT INTO imagens (nome, tipo, caminho) VALUES (?, ?, ?)");
            $stmt_img->bind_param("sss", $nomeOriginal, $tipo, $caminho);
            if ($stmt_img->execute()) {
                $imagem_caminho = $caminho;
            } else {
                die("Erro ao salvar imagem no banco de dados.");
            }
            $stmt_img->close();
        } else {
            die("Erro ao mover o arquivo de imagem.");
        }
    } else {
        die("Nenhum arquivo enviado ou erro no upload.");
    }

    // Inserção no banco de dados usando Prepared Statements puros (preservando UTF-8)
    $stmt = $conn->prepare("INSERT INTO receitas (titulo, ingredientes, modo_preparo, imagem, categoria_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $titulo, $ingredientes, $modo_preparo, $imagem_caminho, $categoria_id, $usuario_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: ../index.php?sucesso=1");
        exit();
    } else {
        echo "Erro ao adicionar receita: " . $stmt->error;
    }
}

// O cabeçalho visual é incluído apenas AGORA, onde começa a renderização da página
include_once('../includes/header.php');
?>

<main class="container pagina-adicionar-receita">
    <h2>Adicionar Receita</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <br>
        <label for="categoria_id">Categoria:</label><br>
        <select name="categoria_id" id="categoria_id" required>
            <option value="">Selecione uma categoria</option>
            <option value="1">Vegetariana</option>
            <option value="2">Vegana</option>
            <option value="3">Salgados</option>
            <option value="4">Massa</option>
            <option value="5">Pães</option>
            <option value="6">Bolo</option>
            <option value="7">Doce</option>
        </select><br><br>

        <div class="form-container">
            <label for="titulo">Título:</label><br>
            <input type="text" name="titulo" id="titulo" required><br><br>

            <label for="ingredientes">Ingredientes:</label><br>
            <textarea name="ingredientes" id="ingredientes" rows="5" required></textarea><br><br>

            <label for="modo_preparo">Modo de Preparo:</label><br>
            <textarea name="modo_preparo" id="modo_preparo" rows="6" required></textarea><br><br>

            <label for="imagem">Imagem da Receita (até 5MB):</label><br>
            <input type="file" name="imagem" id="imagem" accept="image/*" required><br><br>

            <button type="submit">Salvar Receita</button>
        </div>
    </form>
</main>

<?php include_once('../includes/footer.php'); ?>