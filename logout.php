<?php
// Inicia ou retoma a sessão existente
session_start();

// Destroi todos os dados da sessão atual, encerrando o login do usuário
session_destroy();

// Redireciona o usuário para a página de login após o logout
header("Location: login.html");

// Garante que o script será encerrado aqui, evitando que qualquer outro código seja executado depois do redirecionamento
exit();
?>
