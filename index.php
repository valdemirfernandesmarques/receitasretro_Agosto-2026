<?php
// Inicia a sessão, se ainda não iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui o cabeçalho (contém <html>, <head>, <body> e links CSS)
include_once('includes/header.php');

// Criação do banco de dados, se necessário
include_once('includes/create_db.php');
?>

<!-- Início do conteúdo da página -->
<div class="wrapper"> <!-- Garante que o footer fique no final da tela -->
    <main class="container pagina-index">
        <section class="hero">
            <h1 class="titulo">Bem-vindo ao Receitas Retrô 🍰</h1>

        <!-- Bloco de anúncio do Google AdSense: topo da página(Para fazer propaganda e ganhar pelo anuncio)-->
            
            <p class="descricao">Explore receitas nostálgicas, compartilhe sabores e reviva memórias da cozinha da vovó!</p>
            <!--<a href="paginas/receitas.php" class="btn">Explorar Receitas</a> -->
        </section>

        <section class="destaques">
            <h2>Receitas em Destaque</h2>
            <div class="cards">
                <!-- Card de Receita 1 -->
                <div class="card">
                    <img src="assets/img/bolo-caseiro.jpg" alt="Bolo Caseiro">
                    <h3>Bolo Caseiro de Fubá</h3>
                    <p>Clássico da tarde com café, direto da cozinha da vovó!</p>
                    <a href="paginas/ver_receita.php" class="btn-pequeno">Ver Receita</a>
                </div>

                <!-- Card de Receita 2 -->
                <div class="card">
                    <img src="assets/img/pao-caseiro.jpg" alt="Pão Caseiro">
                    <h3>Pão Caseiro Quentinho</h3>
                    <p>Feito com carinho e tradição, perfeito para o café da manhã.</p>
                    <a href="paginas/ver_receita02.php" class="btn-pequeno">Ver Receita</a>
                </div>
                
                <div class="card">
                    <img src="assets/img/torta-de-palmito.jpg" alt="Torta de Palmito">
                    <h3>Torta de Palmito</h3>
                    <p>Essa torta de palmito é uma daquelas delícias que não se esquece..</p>
                    <a href="paginas/ver_receita03.php" class="btn-pequeno">Ver Receita</a>
                </div>

                <div class="card">
                    <img src="assets/img/canjica-nordestina.jpg" alt="Canjica Nordestina">
                    <h3>Canjica Nordestina</h3>
                    <p>Tem gosto de São João, abraço apertado e fogueira acesa no quintal.</p>
                    <a href="paginas/ver_receita04.php" class="btn-pequeno">Ver Receita</a>
                </div>

            </div>
        </section>
    </main>

    <?php
    // Inclui o rodapé (contém o <footer> e </body>, </html>)
    include_once('includes/footer.php');
    ?>
</div>
