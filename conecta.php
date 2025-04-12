<?php
// Define constantes para usuário e senha do banco de dados
define("USER", "root");
define("PASS", "root");

try {
    // Conecta ao MySQL (sem selecionar banco ainda)
    $pdo = new PDO('mysql:host=localhost', USER, PASS);
    // Define o modo de erro para exceções (boas práticas para debug)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Nome do banco de dados
    $dbName = "DadosFornecedor";
    // Cria o banco de dados se ele não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbName");

    // Agora conecta diretamente ao banco criado
    $pdo = new PDO("mysql:host=localhost;dbname=$dbName", USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Caso ocorra algum erro de conexão ou execução, exibe a mensagem
    die("ERRO: " . $e->getMessage());
}
?>
