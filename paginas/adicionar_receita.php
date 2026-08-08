<?php

session_start();

// Garante que a resposta HTML seja UTF-8
header('Content-Type: text/html; charset=UTF-8');

require_once '../includes/conexao.php';

// Garante conexão MySQL em UTF-8 completo
if (isset($conn) && $conn instanceof mysqli) {

    if (!$conn->set_charset("utf8mb4")) {
        die(
            "Erro ao configurar a conexão UTF-8: " .
            $conn->error
        );
    }
}

/**
 * Corrige possíveis textos que tenham vindo
 * de uma codificação UTF-8 incorreta.
 */
function corrigir_utf8($texto)
{
    if ($texto === null || $texto === '') {
        return $texto;
    }

    $texto = (string) $texto;

    /*
     * Detecta os principais sinais de mojibake.
     */
    if (
        strpos($texto, 'Ã') !== false ||
        strpos($texto, 'Â') !== false ||
        strpos($texto, 'â') !== false ||
        strpos($texto, 'ð') !== false ||
        strpos($texto, '�') !== false
    ) {

        $corrigido = @iconv(
            'UTF-8',
            'ISO-8859-1//TRANSLIT',
            $texto
        );

        if ($corrigido !== false) {
            $texto = $corrigido;
        }
    }

    return $texto;
}

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {

    header(
        "Location: ../paginas/cadastro.php?sucesso=1"
    );

    exit();
}

// Verifica se o status do usuário permite adicionar receita
if (
    !isset($_SESSION["usuario_status"]) ||
    $_SESSION["usuario_status"] !== "liberado"
) {

    echo "
    <script>
        alert('Você ainda não tem permissão para adicionar receitas. Aguarde liberação do administrador.');
        window.location.href='login.php';
    </script>
    ";

    exit();
}


// ============================================================
// PROCESSAMENTO DO FORMULÁRIO
// ============================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --------------------------------------------------------
    // RECEBIMENTO DOS DADOS
    // --------------------------------------------------------

    $titulo_bruto = $_POST["titulo"] ?? '';
    $ingredientes_bruto = $_POST["ingredientes"] ?? '';
    $modo_preparo_bruto = $_POST["modo_preparo"] ?? '';

    // Corrige possível codificação incorreta recebida
    $titulo_bruto = corrigir_utf8($titulo_bruto);
    $ingredientes_bruto = corrigir_utf8($ingredientes_bruto);
    $modo_preparo_bruto = corrigir_utf8($modo_preparo_bruto);


    // --------------------------------------------------------
    // NORMALIZAÇÃO DAS QUEBRAS DE LINHA
    // --------------------------------------------------------

    $ingredientes_normalizado = str_replace(
        array("\r\n", "\r"),
        "\n",
        $ingredientes_bruto
    );

    $modo_preparo_normalizado = str_replace(
        array("\r\n", "\r"),
        "\n",
        $modo_preparo_bruto
    );


    // --------------------------------------------------------
    // REMOVE QUEBRAS DE LINHA DUPLICADAS
    // --------------------------------------------------------

    $ingredientes_limpo = preg_replace(
        "/\n+/",
        "\n",
        $ingredientes_normalizado
    );

    $modo_preparo_limpo = preg_replace(
        "/\n+/",
        "\n",
        $modo_preparo_normalizado
    );


    // --------------------------------------------------------
    // LIMPA CADA LINHA
    // --------------------------------------------------------

    $ingredientes_linhas = array_map(
        'trim',
        explode("\n", $ingredientes_limpo)
    );

    $ingredientes_linhas = array_filter(
        $ingredientes_linhas,
        'strlen'
    );

    $ingredientes = implode(
        "\n",
        $ingredientes_linhas
    );


    $modo_preparo_linhas = array_map(
        'trim',
        explode("\n", $modo_preparo_limpo)
    );

    $modo_preparo_linhas = array_filter(
        $modo_preparo_linhas,
        'strlen'
    );

    $modo_preparo = implode(
        "\n",
        $modo_preparo_linhas
    );


    // --------------------------------------------------------
    // TÍTULO
    // --------------------------------------------------------

    $titulo = trim($titulo_bruto);


    // --------------------------------------------------------
    // CATEGORIA E USUÁRIO
    // --------------------------------------------------------

    $categoria_id = isset($_POST["categoria_id"])
        ? intval($_POST["categoria_id"])
        : null;

    $usuario_id = intval($_SESSION["usuario_id"]);

    $imagem_caminho = null;


    // ========================================================
    // VALIDAÇÕES
    // ========================================================

    if ($titulo === '') {
        die("O título da receita é obrigatório.");
    }

    if ($ingredientes === '') {
        die("Os ingredientes são obrigatórios.");
    }

    if ($modo_preparo === '') {
        die("O modo de preparo é obrigatório.");
    }

    if (!$categoria_id) {
        die("Selecione uma categoria.");
    }


    // ========================================================
    // UPLOAD DA IMAGEM
    // ========================================================

    if (
        isset($_FILES['imagem']) &&
        $_FILES['imagem']['error'] === UPLOAD_ERR_OK
    ) {

        $nomeOriginal = basename(
            $_FILES['imagem']['name']
        );

        $tipo = $_FILES['imagem']['type'];

        $extensao = strtolower(
            pathinfo(
                $nomeOriginal,
                PATHINFO_EXTENSION
            )
        );


        // Extensões permitidas
        $tiposPermitidos = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp'
        ];


        if (
            !in_array(
                $extensao,
                $tiposPermitidos,
                true
            )
        ) {

            die(
                "Tipo de arquivo não permitido."
            );
        }


        // Limite de 5 MB
        if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {

            die(
                "A imagem não pode ultrapassar 5 MB."
            );
        }


        // Cria nome único
        $nomeArquivo =
            uniqid("img_", true) .
            "." .
            $extensao;


        $caminho =
            "../uploads/" .
            $nomeArquivo;


        // Move a imagem
        if (
            move_uploaded_file(
                $_FILES['imagem']['tmp_name'],
                $caminho
            )
        ) {

            // Salva informações da imagem no banco
            $stmt_img = $conn->prepare(
                "INSERT INTO imagens
                (nome, tipo, caminho)
                VALUES (?, ?, ?)"
            );


            if (!$stmt_img) {

                die(
                    "Erro ao preparar o cadastro da imagem: " .
                    $conn->error
                );
            }


            $stmt_img->bind_param(
                "sss",
                $nomeOriginal,
                $tipo,
                $caminho
            );


            if ($stmt_img->execute()) {

                $imagem_caminho = $caminho;

            } else {

                // Remove a imagem se não conseguiu
                // salvar no banco
                if (file_exists($caminho)) {
                    unlink($caminho);
                }

                die(
                    "Erro ao salvar imagem no banco de dados: " .
                    $stmt_img->error
                );
            }


            $stmt_img->close();

        } else {

            die(
                "Erro ao mover o arquivo de imagem."
            );
        }

    } else {

        die(
            "Nenhum arquivo enviado ou ocorreu um erro no upload."
        );
    }


    // ========================================================
    // INSERE A RECEITA
    // ========================================================

    $stmt = $conn->prepare(
        "INSERT INTO receitas
        (
            titulo,
            ingredientes,
            modo_preparo,
            imagem,
            categoria_id,
            usuario_id
        )
        VALUES (?, ?, ?, ?, ?, ?)"
    );


    if (!$stmt) {

        die(
            "Erro ao preparar inserção da receita: " .
            $conn->error
        );
    }


    $stmt->bind_param(
        "ssssii",
        $titulo,
        $ingredientes,
        $modo_preparo,
        $imagem_caminho,
        $categoria_id,
        $usuario_id
    );


    if ($stmt->execute()) {

        $stmt->close();
        $conn->close();

        header(
            "Location: ../index.php?sucesso=1"
        );

        exit();

    } else {

        echo "
        <p>
            Erro ao adicionar receita:
            " .
            htmlspecialchars(
                $stmt->error,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            ) .
            "
        </p>
        ";

        $stmt->close();
    }

}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        http-equiv="Content-Type"
        content="text/html; charset=UTF-8"
    >

    <title>Adicionar Receita</title>

</head>

<body class="pagina-adicionar-receita">

    <h2>Adicionar Receita</h2>

    <form
        method="post"
        action=""
        enctype="multipart/form-data"
    >

        <br>

        <label for="categoria_id">
            Categoria:
        </label>

        <select
            name="categoria_id"
            id="categoria_id"
            required
        >

            <option value="">
                Selecione uma categoria
            </option>

            <option value="1">
                Vegetariana
            </option>

            <option value="2">
                Vegana
            </option>

            <option value="3">
                Salgados
            </option>

            <option value="4">
                Massa
            </option>

            <option value="5">
                Pães
            </option>

            <option value="6">
                Bolo
            </option>

            <option value="7">
                Doce
            </option>

        </select>

        <br>
        <br>


        <div class="container">

            <label for="titulo">
                Título:
            </label>

            <br>

            <input
                type="text"
                id="titulo"
                name="titulo"
                required
            >

            <br>
            <br>


            <label for="ingredientes">
                Ingredientes:
            </label>

            <br>

            <textarea
                id="ingredientes"
                name="ingredientes"
                rows="5"
                required
            ></textarea>

            <br>
            <br>


            <label for="modo_preparo">
                Modo de Preparo:
            </label>

            <br>

            <textarea
                id="modo_preparo"
                name="modo_preparo"
                rows="6"
                required
            ></textarea>

            <br>
            <br>


            <label for="imagem">
                Imagem da Receita (até 5MB):
            </label>

            <br>

            <input
                type="file"
                id="imagem"
                name="imagem"
                accept="image/*"
                required
            >

            <br>
            <br>


            <button type="submit">
                Salvar Receita
            </button>

        </div>

    </form>

</body>

</html>

<?php

include_once('../includes/footer.php');

?>