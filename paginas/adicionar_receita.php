<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

require_once '../includes/conexao.php';

if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
}

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../paginas/cadastro.php?sucesso=1");
    exit();
}

if (isset($_SESSION["usuario_status"]) && $_SESSION["usuario_status"] !== "liberado") {
    echo "<script>alert('Você ainda não tem permissão para adicionar receitas. Aguarde liberação do administrador.'); window.location.href='login.php';</script>";
    exit();
}

// Função profunda para sanitizar textos copiados da web
function limpar_entrada_texto($texto) {
    if (empty($texto)) return '';
    
    // 1. Remove palavras residuais de checkboxes do site de origem (check, uncheck, etc)
    $texto = preg_replace('/(check|uncheck)/i', '', $texto);
    
    // 2. Normaliza quebras de linha
    $texto = str_replace(array("\r\n", "\r"), "\n", $texto);
    
    // 3. Limpa linhas consecutivas e espaços
    $linhas = explode("\n", $texto);
    $linhas_limpas = array();
    
    foreach ($linhas as $linha) {
        $l = trim($linha);
        // Remove símbolos estranhos do início das linhas se houver
        $l = preg_replace('/^[^a-zA-Z0-9áàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ\(\)\.,\-\s\/]+/u', '', $l);
        if (!empty($l)) {
            $linhas_limpas[] = $l;
        }
    }
    
    return implode("\n", $linhas_limpas);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo_bruto = $_POST["titulo"];
    $ingredientes_bruto = $_POST["ingredientes"];
    $modo_preparo_bruto = $_POST["modo_preparo"];
    $descricao_bruta = isset($_POST["descricao"]) ? $_POST["descricao"] : $titulo_bruto;

    // Aplica a faxina nos textos
    $titulo = htmlspecialchars(trim($titulo_bruto), ENT_QUOTES, 'UTF-8');
    $descricao = htmlspecialchars(trim($descricao_bruta), ENT_QUOTES, 'UTF-8');
    $ingredientes = htmlspecialchars(limpar_entrada_texto($ingredientes_bruto), ENT_QUOTES, 'UTF-8');
    $modo_preparo = htmlspecialchars(limpar_entrada_texto($modo_preparo_bruto), ENT_QUOTES, 'UTF-8');

    $categoria_id = isset($_POST["categoria_id"]) ? intval($_POST["categoria_id"]) : null;
    $usuario_id = $_SESSION["usuario_id"];
    $imagem_caminho = null;

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

        if (!is_dir("../uploads")) {
            mkdir("../uploads", 0755, true);
        }

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

    $stmt = $conn->prepare("INSERT INTO receitas (titulo, descricao, ingredientes, modo_preparo, imagem, categoria_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssii", $titulo, $descricao, $ingredientes, $modo_preparo, $imagem_caminho, $categoria_id, $usuario_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: ../index.php?sucesso=1");
        exit();
    } else {
        echo "Erro ao adicionar receita: " . $stmt->error;
    }
}

include_once('../includes/header.php');
?>

<main class="conteudo-principal">
    <h2>Adicionar Receita</h2>
    <form method="post" action="" enctype="multipart/form-data">
        
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

        <div class="container">
            <label for="titulo">Título:</label><br>
            <input type="text" name="titulo" id="titulo" required><br><br>

            <label for="descricao">Breve Descrição:</label><br>
            <input type="text" name="descricao" id="descricao" placeholder="Uma breve descrição da receita..." required><br><br>

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