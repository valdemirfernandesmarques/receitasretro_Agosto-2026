<?php
include_once('../includes/header.php');
?>

<main class="container">
<link rel="stylesheet" href="../assets/css/style.css">
    <h1 class="titulo">Receitas</h1>

    <section class="categorias-receitas">
        <p>Escolha uma categoria de receita:</p>
        <ul class="lista-categorias">
            <li><a href="receitas.php?categoria=vegetarianas">Vegetarianas</a></li>
            <li><a href="receitas.php?categoria=veganas">Veganas</a></li>
            <li><a href="receitas.php?categoria=salgados">Salgados</a></li>
            <li><a href="receitas.php?categoria=massas">Massas</a></li>
            <li><a href="receitas.php?categoria=paes">Pães</a></li>
            <li><a href="receitas.php?categoria=bolos">Bolos</a></li>
            <li><a href="receitas.php?categoria=doces">Doces</a></li>
        </ul>
    </section>

    <section class="conteudo-receitas">
        <?php
        // Simulação de exibição por categoria
        if (isset($_GET['categoria'])) {
            $categoria = htmlspecialchars($_GET['categoria']);
            echo "<h2>Receitas da categoria: " . ucfirst($categoria) . "</h2>";
            echo "<p>Aqui aparecerão as receitas cadastradas para a categoria <strong>$categoria</strong>.</p>";
            // Aqui futuramente será exibido o conteúdo vindo do banco
        } else {
            echo "<p>Selecione uma categoria para visualizar as receitas.</p>";
        }
        ?>
    </section>
</main>

<?php
include_once('../includes/footer.php');
?>