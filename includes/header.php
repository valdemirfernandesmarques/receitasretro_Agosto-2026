 <?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função para capturar o nome do arquivo da página atual
function getCurrentPageName() {
    return basename($_SERVER['PHP_SELF']);
}

$currentPage = getCurrentPageName();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receitas Retrô</title>

  <!-- Font Awesome para ícones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- script global do Google AdSense(para ganhar com propagandas)-->

    <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8534248567406604" -->
    <!-- crossorigin="anonymous"></script> -->
 
    <!-- script global do Google AdSense(para ganhar com propagandas: blocos)-->
    <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8534248567406604" -->
    <!-- crossorigin="anonymous"></script> -->
<!-- Anuncio_Topo_Site -->
<!-- <ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-8534248567406604"
     data-ad-slot="7546245038"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins> -->
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>



  
  <!-- CSS principal -->
  <!--<link rel="stylesheet" href="/assets/css/style.css"> -->
  <link rel="stylesheet" href="/assets/css/header.css"> 
  <link rel="stylesheet" href="/assets/css/footer.css">

  <!-- CSS específico por página (auto carregamento com base no nome da página) -->
  <?php
  $cssMap = [
      'index.php' => 'index.css',
      'categoria.php' => 'categoria.css',
      'adicionar_receita.php' => 'adicionar_receita.css',
      'sobre.php' => 'sobre.css',
      'contato.php' => 'contato.css',
      'cadastro.php' => 'cadastro.css',
      'login.php' => 'login.css',
      'dashboard.php' => 'dashboard.css',
      'buscar.php' => 'buscar.css',
      'ver_receita.php' => 'ver_receita.css',
      'ver_receita02.php' => 'ver_receita02.css',
      'ver_receita03.php' => 'ver_receita03.css',
       'ver_receita04.php' => 'ver_receita04.css',
      'politica_privacidade.php' => 'politica_privacidade.css',
      'recuperar_senha.php' => 'recuperar_senha.css'
      
  ];

  if (array_key_exists($currentPage, $cssMap)) {
      echo '<link rel="stylesheet" href="/assets/css/' . $cssMap[$currentPage] . '">' . "\n";
  }
  ?>
</head>

<body class="<?php echo pathinfo($currentPage, PATHINFO_FILENAME); ?>">

<!-- INÍCIO DO CABEÇALHO -->
<header class="site-header">
  <nav class="navbar"> <!--  <nav class="navbar" role="navigation" aria-label="Menu principal">   -->
    
    <!-- Logo do site -->
    <div class="logo">
      <a href="index.php">
        <img src="/assets/img/receitas-retro-logo.jpeg" alt="Receitas Retrô">
      </a>
    </div>

    <!-- Barra de pesquisa -->
    <div class="search-bar"> <!-- role="search">-->
      <form action="/paginas/buscar.php" method="GET"> <!-- aria-label="Buscar receitas no site"> -->
        <input type="text" name="q" placeholder="Buscar receitas..." required>
        <button type="submit"><i class="fas fa-search"></i></button>
      </form>
    </div>

    <!-- Menu de navegação -->
    <ul class="menu">
      <li><a href="/index.php">Início</a></li>

      <!-- Submenu de receitas por categoria -->
      <li class="dropdown">
        <a href="#">Receitas <i class="fas fa-caret-down"></i></a>
        <ul class="dropdown-content">
          <li><a href="/paginas/categoria.php?cat=vegetarianas">Vegetarianas</a></li>
          <li><a href="/paginas/categoria.php?cat=veganas">Veganas</a></li>
          <li><a href="/paginas/categoria.php?cat=salgados">Salgados</a></li>
          <li><a href="/paginas/categoria.php?cat=massas">Massas</a></li>
          <li><a href="/paginas/categoria.php?cat=paes">Pães</a></li>
          <li><a href="/paginas/categoria.php?cat=bolos">Bolos</a></li>
          <li><a href="/paginas/categoria.php?cat=doces">Doces</a></li>
        </ul>
      </li>

      <li><a href="/paginas/adicionar_receita.php">Adicionar Receita</a></li>
      <li><a href="/paginas/saude.php">Saúde</a></li>
      <li><a href="/paginas/sobre.php">Sobre</a></li>
      <li><a href="/paginas/contato.php">Contato</a></li>

      <!-- Verifica se o usuário está logado -->
      <?php if (isset($_SESSION['usuario_id'])): ?>
        <li><a href="/paginas/logout.php">Sair</a></li>
      <?php else: ?>
        <li><a href="/paginas/login.php">Login</a></li>
        <li><a href="/paginas/cadastro.php">Cadastrar</a></li>
      <?php endif; ?>
    </ul>

    <!-- Menu toggle para mobile (será ativado via JavaScript se desejar no futuro) -->
    <!-- <button class="menu-toggle" onclick="document.querySelector('.menu').classList.toggle('active')">☰</button> -->

  </nav>
</header>
<!-- FIM DO CABEÇALHO -->