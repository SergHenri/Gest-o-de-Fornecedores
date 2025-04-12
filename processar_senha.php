<?php
session_start();

// Conexão com o banco de dados
require 'conecta.php';  // Inclua o código de conexão aqui, se necessário

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $nova_senha = $_POST['nova_senha'];

    // Validação simples da nova senha
    if (empty($nova_senha)) {
        echo 'A nova senha não pode estar vazia.';
        exit();
    }

    // Atualiza a senha no banco de dados
    $stmt = $pdoUsuarioSistema->prepare("UPDATE users SET senha = :senha WHERE usuario = :usuario");
    $stmt->bindParam(':senha', $nova_senha);
    $stmt->bindParam(':usuario', $usuario);
    
    if ($stmt->execute()) {
        echo 'Senha alterada com sucesso!';
    } else {
        echo 'Erro ao alterar a senha.';
    }
}
?>
