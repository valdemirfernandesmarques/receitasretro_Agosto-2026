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
        <img src="../assets/img/pao-caseiro.jpg" alt="Imagem da Receita de Pão Caseiro">
    </div>

    <!-- Título da Receita -->
    <h1 class="receita-titulo">Pão Caseiro</h1>

    <!-- Ingredientes -->
    <section class="receita">
        <h2>Ingredientes</h2>
        <textarea readonly rows="8">
1 kg de farinha de trigo
2 copos de leite morno
2 colheres (sopa) de açúcar
1 colher (sopa) de sal
2 ovos
1/2 copo de óleo
2 tabletes de fermento biológico fresco (30g)
        </textarea>

        <!-- Modo de Preparo -->
        <h2>Modo de Preparo</h2>
        <textarea readonly rows="10">
1. Dissolva o fermento no leite morno com o açúcar e deixe descansar por 10 minutos.
2. Em uma tigela, misture a farinha, sal, ovos, óleo e o leite com fermento.
3. Sove a massa por 10 minutos até ficar homogênea.
4. Cubra e deixe descansar por 1 hora.
5. Modele os pães e coloque em formas untadas.
6. Deixe crescer por mais 30 minutos.
7. Asse em forno pré-aquecido a 180°C por cerca de 35 a 40 minutos.
        </textarea>
    </section>

</main>


<!-- script global do Google AdSense(para ganhar com propagandas: blocos)-->
     


<?php
// Inclui o rodapé da página
include_once('../includes/footer.php');
?>