
<?php
// Arquivo: receitasretro/recuperar_senha.php

session_start();
require_once('config.php'); // Inclui as configurações globais da raiz
?>

<?php
// Inclui o cabeçalho padrão, localizado em 'includes/'
// O caminho é relativo a 'recuperar_senha.php'
include_once('includes/header.php');
?>

<main class="form-container">
    <section class="form-box">
        <h2>Recuperar Senha</h2>
        <?php
        if (isset($_SESSION['mensagem'])) {
            echo '<p class="mensagem">' . htmlspecialchars($_SESSION['mensagem']) . '</p>';
            unset($_SESSION['mensagem']);
        }
        ?>
        <form action="admin/processa_recuperacao.php" method="POST">
            <div class="form-group">
                <label for="email">E-mail Cadastrado:</label>
                <input type="email" id="email" name="email" required placeholder="Digite seu e-mail">
            </div>
            <button type="submit">Enviar Link de Recuperação</button>
        </form>
        <p>Lembrou da senha? <a href="paginas/login.php">Fazer Login</a></p>
    </section>
</main>

<?php
// Inclui o rodapé padrão, localizado em 'includes/'
include_once('includes/footer.php');
?>