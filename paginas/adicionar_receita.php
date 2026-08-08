<?php
session_start();
require_once '../includes/conexao.php';
include_once('../includes/header.php');

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../paginas/cadastro.php?sucesso=1");
    exit();
}

// Verifica se o status do usuário permite adicionar receita
if ($_SESSION["usuario_status"] !== "liberado") {
    echo "<script>alert('Você ainda não tem permissão para adicionar receitas. Aguarde liberação do administrador.'); window.location.href='login.php';</script>";
    exit();
}

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo_bruto = $_POST["titulo"];
    $ingredientes_bruto = $_POST["ingredientes"];
    $modo_preparo_bruto = $_POST["modo_preparo"];

    // --- INÍCIO DA LÓGICA DE LIMPEZA E NORMALIZAÇÃO ---
    $ingredientes_normalizado = str_replace(array("\r\n", "\r"), "\n", $ingredientes_bruto);
    $modo_preparo_normalizado = str_replace(array("\r\n", "\r"), "\n", $modo_preparo_bruto);

    $ingredientes_limpo = preg_replace("/\n+/", "\n", $ingredientes_normalizado);
    $modo_preparo_limpo = preg_replace("/\n+/", "\n", $modo_preparo_normalizado);

    $ingredientes_linhas = array_map('trim', explode("\n", $ingredientes_limpo));
    $ingredientes = implode("\n", array_filter($ingredientes_linhas, 'strlen'));

    $modo_preparo_linhas = array_map('trim', explode("\n", $modo_preparo_limpo));
    $modo_preparo = implode("\n", array_filter($modo_preparo_linhas, 'strlen'));

    $titulo = htmlspecialchars(trim($titulo_bruto));
    $ingredientes = htmlspecialchars($ingredientes);
    $modo_preparo = htmlspecialchars($modo_preparo);
    // --- FIM DA LÓGICA DE LIMPEZA E NORMALIZAÇÃO ---

    $categoria_id = isset($_POST["categoria_id"]) ? intval($_POST["categoria_id"]) : null;
    $usuario_id = $_SESSION["usuario_id"];
    $imagem_caminho = null;

    // --- UPLOAD DA IMAGEM (CLOUDINARY) ---
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $nomeOriginal = basename($_FILES['imagem']['name']);
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

        $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extensao, $tiposPermitidos)) {
            die("Tipo de arquivo não permitido.");
        }

        $tmpFilePath = $_FILES['imagem']['tmp_name'];

        // --- CONFIGURAÇÃO DO CLOUDINARY ---
        // Preencha com as suas credenciais do Cloudinary:
        $cloudName = 'SEU_CLOUD_NAME';
        $apiKey    = 'SUA_API_KEY';
        $apiSecret = 'SEU_API_SECRET';

        $timestamp = time();
        $paramsToSign = "timestamp=" . $timestamp;
        $signature = sha1($paramsToSign . $apiSecret);

        $postData = [
            'file' => new CURLFile($tmpFilePath),
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (!$err && isset($data['secure_url'])) {
            // Upload no Cloudinary realizado com sucesso -> URL permanente
            $imagem_caminho = $data['secure_url'];
        } else {
            // Fallback para pasta local se o Cloudinary não estiver configurado/responder
            if (!is_dir("../uploads")) {
                mkdir("../uploads", 0755, true);
            }
            $nomeArquivo = uniqid("img_", true) . "." . $extensao;
            $caminhoLocal = "../uploads/" . $nomeArquivo;

            if (move_uploaded_file($tmpFilePath, $caminhoLocal)) {
                $imagem_caminho = $caminhoLocal;
            } else {
                die("Erro ao salvar imagem localmente ou no Cloudinary.");
            }
        }

        // Salva o registro na tabela de imagens
        $tipo = $_FILES['imagem']['type'];
        $stmt_img = $conn->prepare("INSERT INTO imagens (nome, tipo, caminho) VALUES (?, ?, ?)");
        $stmt_img->bind_param("sss", $nomeOriginal, $tipo, $imagem_caminho);
        $stmt_img->execute();
        $stmt_img->close();

    } else {
        die("Nenhum arquivo enviado ou erro no upload.");
    }

    // Inserção da receita no banco de dados
    $stmt = $conn->prepare("INSERT INTO receitas (titulo, ingredientes, modo_preparo, imagem, categoria_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $titulo, $ingredientes, $modo_preparo, $imagem_caminho, $categoria_id, $usuario_id);

    if ($stmt->execute()) {
        header("Location: ../index.php?sucesso=1");
        exit();
    } else {
        echo "Erro ao adicionar receita: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Adicionar Receita</title>
</head>

<body class="pagina-adicionar-receita">
    <h2>Adicionar Receita</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <br>
        <select name="categoria_id" required>
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
            <input type="text" name="titulo" required><br><br>

            <label for="ingredientes">Ingredientes:</label><br>
            <textarea name="ingredientes" rows="5" required></textarea><br><br>

            <label for="modo_preparo">Modo de Preparo:</label><br>
            <textarea name="modo_preparo" rows="6" required></textarea><br><br>

            <label for="imagem">Imagem da Receita (até 5MB):</label><br>
            <input type="file" name="imagem" accept="image/*" required><br><br>

            <button type="submit">Salvar Receita</button>
        </div>
    </form>
</body>

</html>
<?php include_once('../includes/footer.php'); ?>