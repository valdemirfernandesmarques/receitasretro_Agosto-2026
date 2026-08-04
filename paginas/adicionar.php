<?php
include_once('../includes/header.php');
include_once('../includes/auth.php'); // Verifica se o usuário está logado
?>

<main class="container">
    <link rel="stylesheet" href="../assets/css/style.css">
    <h1 class="titulo">Adicionar Receita</h1>

    <?php if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['status'] != 'ativo') : ?>
        <p class="aviso">Você precisa estar logado e ter permissão para adicionar receitas.</p>
        <a href="../admin/login.php" class="btn">Fazer Login</a>
    <?php else : ?>
        <form action="adicionar_receita.php" method="POST" enctype="multipart/form-data" class="formulario-receita">
            <label for="titulo">Título da Receita:</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria" required>
                <option value="vegetariana">Vegetariana</option>
                <option value="vegana">Vegana</option>
                <option value="salgado">Salgado</option>
                <option value="massa">Massa</option>
                <option value="paes">Pães</option>
                <option value="bolo">Bolo</option>
                <option value="doce">Doce</option>
            </select>

            <label for="ingredientes">Ingredientes:</label>
            <textarea id="ingredientes" name="ingredientes" rows="4" required></textarea>

            <label for="modo_preparo">Modo de Preparo:</label>
            <textarea id="modo_preparo" name="modo_preparo" rows="6" required></textarea>

            <label for="imagem">Imagem da Receita (até 5MB):</label>
            <input type="file" id="imagem" name="imagem" accept="image/*" required>

            <button type="submit" class="btn">Enviar Receita</button>
        </form>

        <div class="compartilhar">
            <p>Compartilhe sua receita:</p>
            <a href="#" class="compartilhar-link">WhatsApp</a>
            <a href="#" class="compartilhar-link">Facebook</a>
            <a href="#" class="compartilhar-link">Instagram</a>
        </div>
    <?php endif; ?>
</main>

<?php
include_once('../includes/footer.php');
?>