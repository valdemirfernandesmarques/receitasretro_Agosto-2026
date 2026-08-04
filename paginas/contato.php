
<?php
// Verifica se o formulário foi enviado
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Ajuste o caminho conforme sua estrutura

$mensagemStatus = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';

    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP do Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'receitasretro.sabor@gmail.com'; // Seu e-mail
        $mail->Password   = 'ebps fsdf ntoh pzqa'; // Sua senha de app (não é a senha normal do Gmail!)
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom($email, $nome); 
        $mail->addAddress('receitasretro.sabor@gmail.com', 'Receitas Retrô');

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; // Aqui é para os caracteres especiais 
        $mail->Subject = 'Nova mensagem do site ReceitasRetrô';
        $mail->Body    = "<strong>Nome:</strong> $nome<br><strong>Email:</strong> $email<br><strong>Mensagem:</strong><br>$mensagem";

        $mail->send();
        $mensagemStatus = '<p class="sucesso">Mensagem enviada com sucesso!</p>';
    } catch (Exception $e) {
        $mensagemStatus = '<p class="erro">Erro ao enviar: ' . $mail->ErrorInfo . '</p>';
    }
}
?>

<?php include_once('../includes/header.php'); ?>

<main class="container-contato">
    
    <h1 class="titulo-pagina">Fale Conosco</h1>

    <p class="descricao-pagina">
        Tem dúvidas, sugestões ou quer entrar em contato conosco? Preencha o formulário abaixo e responderemos o mais breve possível.
    </p>

    <!-- Mostra mensagem de sucesso ou erro -->
    <?php if (!empty($mensagemStatus)) echo $mensagemStatus; ?>

    <form action="contato.php" method="POST" class="formulario-contato">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required placeholder="Seu nome completo">
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required placeholder="seuemail@exemplo.com">
        </div>

        <div class="form-group">
            <label for="mensagem">Mensagem:</label>
            <textarea id="mensagem" name="mensagem" rows="5" required placeholder="Escreva aqui sua mensagem..."></textarea>
        </div>

        <button type="submit" class="botao-enviar">Enviar Mensagem</button>
    </form>
</main>
<?php include_once('../includes/footer.php'); ?>
