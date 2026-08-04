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
        <img src="../assets/img/torta-de-palmito.jpg" alt="Imagem da Receita de Torta de Palmito">
    </div>

    <!-- Título da Receita -->
    <h1 class="receita-titulo">Torta de Palmito</h1>

    <!-- Ingredientes -->
    <section class="receita">
        <h2>Ingredientes (18 porções)</h2>
        
        <h3>Massa</h3>
        <textarea readonly rows="10">
700 g de farinha de trigo
2 ovos
Sal a gosto
1 colher (café) de açúcar
1 colher (chá) de fermento biológico
3 colheres (sopa) de manteiga
1 xícara de leite
1 gema para pincelar
        </textarea>

        <h3>Recheio</h3>
        <textarea readonly rows="12">
1 vidro de palmito em pedaços
1 colher da água do palmito
1 colher de óleo
3 dentes de alho picados
1 cebola picada
1/2 lata de ervilha
1/2 lata de milho
Salsinha picada a gosto
Sal a gosto
1 xícara de leite
2 colheres rasas de farinha de trigo
2 tomates picados
        </textarea>

        <!-- Modo de Preparo -->
        <h2>Modo de Preparo (2h 5min)</h2>
        <textarea readonly rows="20">
Massa:
1. Colocar todos os ingredientes em um refratário e mexer bem com as mãos, até desgrudar das mãos.
2. Forme uma bola e divida em duas partes.
3. Abra bem as duas partes com um rolo.

Recheio:
4. Colocar óleo, alho e cebola em uma panela e deixe até começar a dourar.
5. Acrescente tomate, palmito, água do palmito, ervilha e milho.
6. Mexa bem, espere o tomate murchar.
7. Em um copo, coloque leite e farinha e mexa até desmanchar a farinha.
8. Acrescente essa mistura na panela com os outros ingredientes.
9. Se engrossar muito, acrescente um pouco de leite.
10. Mexa um pouco e desligue. Reserve.

Montagem:
11. Abra as 2 partes da massa.
12. Unte uma assadeira com manteiga e farinha.
13. Preencha o fundo da assadeira com uma parte da massa, apertando nas laterais para grudar na forma.
14. Espalhe o recheio sobre esta parte.
15. Cubra o recheio com a outra parte da massa, pressionando os lados para grudar.
16. Se sobrar massa, use para enfeitar.
17. Bata levemente a gema e pincele a massa.
18. Leve ao forno em fogo médio por cerca de 50 minutos (ou até dourar).
19. Retire do forno, espere esfriar e desenforme.
        </textarea>
    </section>

</main>


<!-- script global do Google AdSense(para ganhar com propagandas: blocos)-->
     


<?php
// Inclui o rodapé da página
include_once('../includes/footer.php');
?>