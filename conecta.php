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

    // Cria a tabela FORNECEDOR se ela ainda não existir
    $query = "CREATE TABLE IF NOT EXISTS FORNECEDOR (
        ID INT PRIMARY KEY AUTO_INCREMENT,   -- ID numérico único e autoincrementável
        NOME VARCHAR(255) NOT NULL,          -- Nome do fornecedor, obrigatório
        CNPJ VARCHAR(18) UNIQUE NOT NULL     -- CNPJ único e obrigatório
    )";
    // Executa a criação da tabela
    $pdo->exec($query);

} catch (PDOException $e) {
    // Caso ocorra algum erro de conexão ou execução, exibe a mensagem
    die("ERRO: " . $e->getMessage());
}
?>
