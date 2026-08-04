<?php
session_start();
$_SESSION = array();       // Limpa todas as variáveis da sessão
session_destroy();         // Destrói a sessão
header('Location: /index.php');  // Redireciona para a tela inicial (ou login)
exit();



//session_start();
//session_destroy();
//header('Location: login.php');
//exit(); 