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
        <img src="../assets/img/bolo-caseiro.jpg" alt="Imagem da Receita">
    </div>

    <!-- Título da Receita -->
    <h1 class="receita-titulo">Bolo de Fubá Cremoso</h1>

    <!-- Ingredientes -->
    <section class="receita">
        <h2>Ingredientes</h2>
        <textarea readonly rows="6">
3 ovos
2 xícaras de açúcar
2 xícaras de fubá
1/2 xícara de óleo
2 xícaras de leite
1 colher de sopa de fermento em pó
        </textarea>

        <!-- Modo de Preparo -->
        <h2>Modo de Preparo</h2>
        <textarea readonly rows="8">
1. Bata os ovos com o açúcar, fubá, óleo e leite no liquidificador.
2. Misture o fermento delicadamente com uma colher.
3. Despeje em forma untada e leve ao forno a 180°C por 35 a 40 minutos.
        </textarea>
    </section>

</main>



<!-- script global do Google AdSense(para ganhar com propagandas: blocos)-->
     

<?php
// Inclui o rodapé da página
include_once('../includes/footer.php');
?>