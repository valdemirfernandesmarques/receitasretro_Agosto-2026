<?php
session_start(); // Inicia a sessão para acessar variáveis de sessão e definir mensagens.
require_once '../includes/conexao.php'; // Inclui o arquivo de conexão com o banco de dados.

// --- Verificação de Acesso do Administrador ---
// Garante que apenas o administrador pode executar as ações neste script.
if (!isset($_SESSION["usuario_email"]) || $_SESSION["usuario_email"] !== "admin@retro.com") {
    header("Location: ../paginas/login.php"); // Redireciona se não for administrador.
    exit();
}

// Verifica se a requisição é do tipo POST (formulário enviado)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém o ID da receita e a ação a ser executada do POST.
    // Garante que o ID seja um inteiro e que a ação não esteja vazia.
    $receita_id = isset($_POST['receita_id']) ? intval($_POST['receita_id']) : 0;
    $acao = isset($_POST['acao']) ? $_POST['acao'] : '';

    // Se o ID da receita for inválido (0), define uma mensagem de erro e redireciona.
    if ($receita_id === 0) {
        $_SESSION['mensagem_admin'] = "Erro: ID da receita inválido.";
        header("Location: dashboard.php");
        exit();
    }

    // --- Lógica para Liberar a Receita ---
    if ($acao === "liberar") {
        // Prepara a consulta para atualizar o status da receita para 'liberado'.
        $stmt = $conn->prepare("UPDATE receitas SET status = 'liberado' WHERE id = ? AND status = 'pendente'");
        $stmt->bind_param("i", $receita_id);
        
        if ($stmt->execute()) {
            // Verifica se alguma linha foi afetada para confirmar que a atualização ocorreu.
            if ($stmt->affected_rows > 0) {
                $_SESSION['mensagem_admin'] = "Receita liberada com sucesso!";
            } else {
                $_SESSION['mensagem_admin'] = "Receita não encontrada ou já estava liberada.";
            }
        } else {
            $_SESSION['mensagem_admin'] = "Erro ao liberar receita: " . $stmt->error;
        }
        $stmt->close(); // Fecha o statement.
    } 
    // --- Lógica para Recusar/Excluir a Receita ---
    elseif ($acao === "recusar") {
        // Primeiro, busca o caminho da imagem associada à receita para excluí-la do servidor.
        $stmt_img = $conn->prepare("SELECT imagem FROM receitas WHERE id = ? AND status = 'pendente'");
        $stmt_img->bind_param("i", $receita_id);
        $stmt_img->execute();
        $result_img = $stmt_img->get_result();

        if ($result_img->num_rows > 0) {
            $row_img = $result_img->fetch_assoc();
            $caminho_imagem = $row_img['imagem']; // Obtém o caminho da imagem.

            // Verifica se o caminho da imagem existe, se o arquivo existe no servidor e se é um arquivo real.
            // Isso previne erros e tentativas de apagar diretórios ou arquivos inválidos.
            if ($caminho_imagem && file_exists("../" . $caminho_imagem) && is_file("../" . $caminho_imagem)) {
                unlink("../" . $caminho_imagem); // Deleta o arquivo físico da pasta 'uploads/'.
                // Nota: Se você tiver uma tabela 'imagens' separada, precisaria também deletar o registro de lá.
                // Ex: $stmt_del_img = $conn->prepare("DELETE FROM imagens WHERE caminho = ?"); ...
            }
        }
        $stmt_img->close(); // Fecha o statement da busca de imagem.

        // Em seguida, deleta a receita do banco de dados.
        $stmt_delete = $conn->prepare("DELETE FROM receitas WHERE id = ? AND status = 'pendente'");
        $stmt_delete->bind_param("i", $receita_id);
        
        if ($stmt_delete->execute()) {
             if ($stmt_delete->affected_rows > 0) {
                $_SESSION['mensagem_admin'] = "Receita recusada e excluída com sucesso!";
             } else {
                $_SESSION['mensagem_admin'] = "Receita não encontrada ou não estava pendente para exclusão.";
             }
        } else {
            $_SESSION['mensagem_admin'] = "Erro ao recusar/excluir receita: " . $stmt_delete->error;
        }
        $stmt_delete->close(); // Fecha o statement da exclusão.

    } 
    // --- Ação Inválida ---
    else {
        $_SESSION['mensagem_admin'] = "Ação inválida.";
    }

    $conn->close(); // Fecha a conexão com o banco de dados após todas as operações.
    header("Location: dashboard.php"); // Redireciona de volta para o dashboard.
    exit(); // Encerra o script.

} else {
    // Se a requisição não for POST (acesso direto à página), redireciona para o dashboard.
    header("Location: dashboard.php");
    exit();
}
?>