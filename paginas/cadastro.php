<?php include_once('../includes/header.php'); ?>

<main class="container">
    <section class="cadastro">
        <h2>Cadastre-se</h2>
        <p>Preencha os dados abaixo para criar sua conta e aproveitar todos os recursos do Receitas Retrô!</p>
        <form action="cadastro_usuario.php" method="POST" class="form-cadastro">
            <label for="nome">Nome completo:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>

            <!--<label for="telefone">Telefone:</label> -->
           <!-- <input type="tel" id="telefone" name="telefone" required placeholder="(99) 99999-9999"> -->

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <label for="confirmar_senha">Confirmar Senha:</label> 
            <input type="password" id="confirmar_senha" name="confirmar_senha" required> 

            <button type="submit">Criar Conta</button>
        </form>

        <p class="login-link">Já tem uma conta? <a href="login.php">Faça login</a></p>
    </section>
</main>

<?php include_once('../includes/footer.php'); ?>