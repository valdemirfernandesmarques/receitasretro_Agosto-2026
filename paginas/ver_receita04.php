<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui o cabeçalho da página
include_once('../includes/header.php');
?>

<!-- Link para o CSS exclusivo da página -->
<link rel="stylesheet" href="../assets/ver_receita.css">

<main class="container">
    
    <!-- Imagem da Receita -->
    <div class="receita-imagem">
        <img src="../assets/img/canjica-nordestina.jpg" alt="Imagem da Receita de Canjica Nordestina">
    </div>

    <!-- Título da Receita -->
    <h1 class="receita-titulo">Canjica Nordestina</h1>
    <!--<p class="receita-frase">Tradição em cada colherada, uma delícia que aquece o coração!</p> --

    <!-- Ingredientes -->
    <section class="receita">
        <h2>Ingredientes (30 porções)</h2>
        <textarea readonly rows="8">
- 10 espigas de milho
- 1 litro de leite de coco
- 2 xícaras rasas de açúcar
- 1 colher de sobremesa de sal
- 2 colheres de sopa de manteiga
        </textarea>

        <!-- Modo de Preparo -->
        <h2>Modo de Preparo</h2>
        <textarea readonly rows="12">
1. Lave bem as espigas e retire os milhos com uma faca bem afiada.
2. Bata os grãos no liquidificador com metade do leite de coco.
3. Coe a mistura em uma peneira fina, extraindo o caldo.
4. Leve ao fogo com o sal, mexendo sempre.
5. Acrescente o restante do leite de coco até engrossar.
6. Quando a massa começar a cair lentamente da colher, adicione o açúcar.
7. Mexa vigorosamente até incorporar bem.
8. Adicione a manteiga e cozinhe por cerca de 30 minutos.
9. Ajuste o sal e açúcar, se necessário.
10. Sirva em taças ou travessas com canela por cima.
        </textarea>
    </section>

</main>


<!-- script global do Google AdSense(para ganhar com propagandas: blocos)-->
     

<?php
// Inclui o rodapé da página
include_once('../includes/footer.php');
?>