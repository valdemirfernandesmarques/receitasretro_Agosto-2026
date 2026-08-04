<?php
// Arquivo: receitasretro/funcoes.php

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmailRedefinicao($destinatarioEmail, $destinatarioNome, $token) {
    $mail = new PHPMailer(true);

    try {
        // Configurações SMTP (estão corretas)
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port = MAIL_PORT;
        $mail->CharSet = 'UTF-8';

        // Definindo o REMETENTE e o RECIPIENTE
        // O setFrom DEVE ser o seu MAIL_USERNAME se você está usando o SMTP do Gmail
        $mail->setFrom(MAIL_USERNAME, 'Receitas Retrô');

        // Adicionar o destinatário (ESTA LINHA ESTÁ CORRETA E É ESSENCIAL)
        $mail->addAddress($destinatarioEmail, $destinatarioNome);

        // Opcional, mas recomendado: Adiciona um "Responder Para"
        // Isso garante que se o usuário clicar em responder, a resposta vá para o seu e-mail de suporte.
        // Se você não quiser que as pessoas respondam a este e-mail (por ser automatizado), pode omitir.
        // $mail->addReplyTo(MAIL_USERNAME, 'Receitas Retrô Suporte');

        // Conteúdo do e-mail (já está correto)
        $mail->isHTML(true);
        $mail->Subject = 'Redefinição de Senha - Receitas Retrô';
        $linkRedefinicao = BASE_URL . 'redefinir_senha.php?token=' . $token;
        $mail->Body = 'Olá ' . htmlspecialchars($destinatarioNome) . ',<br><br>'
                    . 'Recebemos uma solicitação de redefinição de senha para sua conta no Receitas Retrô.<br>'
                    . 'Para redefinir sua senha, clique no link abaixo:<br><br>'
                    . '<a href="' . $linkRedefinicao . '">' . $linkRedefinicao . '</a><br><br>'
                    . 'Este link é válido por 1 hora. Se você não solicitou esta redefinição, por favor, ignore este e-mail.<br><br>'
                    . 'Atenciosamente,<br>Equipe Receitas Retrô';
        $mail->AltBody = 'Olá ' . htmlspecialchars($destinatarioNome) . ', Recebemos uma solicitação de redefinição de senha para sua conta no Receitas Retrô. Para redefinir sua senha, copie e cole o seguinte link no seu navegador: ' . $linkRedefinicao . ' Este link é válido por 1 hora. Se você não solicitou esta redefinição, por favor, ignore este e-mail. Atenciosamente, Equipe Receitas Retrô';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail de redefinição para " . $destinatarioEmail . ": " . $mail->ErrorInfo);
        return false;
    }
}