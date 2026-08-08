<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

require_once '../includes/conexao.php';
require_once '../includes/upload_cloudinary.php'; // Inclui a integração com Cloudinary

// Configura o charset do banco para UTF-8
if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
}

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../paginas/cadastro.php?sucesso=1");
    exit();
}

// Verifica se o status do usuário permite adicionar receita
if (isset($_SESSION["usuario_status"]) && $_SESSION["usuario_status"] !== "liberado") {
    echo "<script>alert('Você ainda não tem permissão para adicionar receitas. Aguarde liberação do administrador.'); window.location.href='login.php';</script>";
    exit();
}

// Processa o envio do formulário ANTES de incluir qualquer HTML/Header
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo_bruto = $_POST["titulo"];
    $ingredientes_bruto = $_POST["ingredientes"];
    $modo_preparo_bruto = $_POST["modo_preparo"];
    $descricao_bruta = isset($_POST["descricao"]) ? $_POST["descricao"] : $titulo_bruto;

    // --- LIMPEZA E NORMALIZAÇÃO ---
    $ingredientes_normalizado = str_replace(array("\r\n", "\r"), "\n", $ingredientes_bruto);
    $modo_preparo_normalizado = str_replace(array("\r\n", "\r"), "\n", $modo_preparo_bruto);

    $ingredientes_limpo = preg_replace("/\n+/", "\n", $ingredientes_normalizado);
    $modo_preparo_limpo = preg_replace("/\n+/", "\n", $modo_preparo_normalizado);

    $ingredientes_linhas = array_map('trim', explode("\n", $ingredientes_limpo));
    $ingredientes = implode("\n", array_filter($ingredientes_linhas, 'strlen'));

    $modo_preparo_linhas = array_map('trim', explode("\n", $modo_preparo_limpo));
    $modo_preparo = implode("\n", array_filter($modo_preparo_linhas, 'strlen'));

    $titulo = htmlspecialchars(trim($titulo_bruto), ENT_QUOTES, 'UTF-8');
    $descricao = htmlspecialchars(trim($descricao_bruta), ENT_QUOTES, 'UTF-8');
    $ingredientes = htmlspecialchars($ingredientes, ENT_QUOTES, 'UTF-8');
    $modo_preparo = htmlspecialchars($modo_preparo, ENT_QUOTES, 'UTF-8');

    $categoria_id = isset($_POST["categoria_id"]) ? intval($_POST["categoria_id"]) : null;
    $usuario_id = $_SESSION["usuario_id"];
    $imagem_caminho = null;

    // Upload da imagem usando Cloudinary
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $nomeOriginal = basename($_FILES['imagem']['name']);
        $tipo = $_FILES['imagem']['type'];
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

        $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extensao, $tiposPermitidos)) {
            die("Tipo de arquivo não permitido.");
        }

        // Faz o upload para a nuvem (Cloudinary) e obtém a URL segura (https://...)
        $url_cloudinary = upload_para_cloudinary($_FILES['imagem']['tmp_name']);

        if ($url_cloudinary) {
            $imagem_caminho = $url_cloudinary;

            // Opcional: Salva no registro da tabela de imagens
            $stmt_img = $conn->prepare("INSERT INTO imagens (nome, tipo, caminho) VALUES (?, ?, ?)");
            $stmt_img->bind_param("sss", $nomeOriginal, $tipo, $imagem_caminho);
            $stmt_img->execute();
            $stmt_img->close();
        } else {
            die("Erro ao realizar o upload da imagem para a nuvem.");
        }
    } else {
        die("Nenhum arquivo enviado ou erro no upload.");
    }

    // Inserção da receita no banco salvando a URL permanente da imagem
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

// Inclui o cabeçalho somente no momento de renderizar a interface
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
            <input name="ingredientes" id="ingredientes" rows="5" required></input><br><br>

            <label for="modo_preparo">Modo de Preparo:</label><br>
            <textarea name="modo_preparo" id="modo_preparo" rows="6" required></textarea><br><br>

            <label for="imagem">Imagem da Receita (até 5MB):</label><br>
            <input type="file" name="imagem" id="imagem" accept="image/*" required><br><br>

            <button type="submit">Salvar Receita</button>
        </div>
    </form>
</main>

<?php include_once('../includes/footer.php'); ?>