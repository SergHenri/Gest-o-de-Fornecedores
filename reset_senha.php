<?php
session_start();

// Definindo as constantes de conexão
define("USER", "root");
define("PASS", "root");

try {
    // Conecta ao banco de dados DadosFornecedor
    $pdoDadosFornecedor = new PDO("mysql:host=localhost;dbname=DadosFornecedor", USER, PASS);
    $pdoDadosFornecedor->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados DadosFornecedor: " . $e->getMessage());
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];

    // Valida os dados do formulário
    if (empty($usuario) || empty($email)) {
        echo 'Preencha todos os campos.';
        exit();
    }

    // Verifica no banco de dados DadosFornecedor se o usuário e e-mail estão corretos
    $stmt = $pdoDadosFornecedor->prepare("SELECT * FROM CONTRATOS WHERE USUARIO = :usuario AND EMAIL = :email");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Se o usuário e e-mail não forem encontrados
    if ($stmt->rowCount() === 0) {
        echo 'Usuário ou e-mail não encontrados.';
        exit();
    }

    // Envia um link para o usuário resetar a senha
    $link = "http://localhost/alterar_senha.php?usuario=$usuario&email=$email";
    $mensagem = "Clique no link para resetar sua senha: $link";

    // Envia o e-mail
    if (mail($email, "Reset de Senha", $mensagem)) {
        echo "Instruções para resetar a senha foram enviadas para seu e-mail.";
    } else {
        echo "Erro ao enviar o e-mail.";
    }
}
?>
