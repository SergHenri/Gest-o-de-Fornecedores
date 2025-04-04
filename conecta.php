<?php
define("USER", "root");
define("PASS", "root");

try {
    // Conectar ao MySQL
    $pdo = new PDO('mysql:host=localhost', USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criar o banco de dados se não existir
    $dbName = "DadosFornecedor";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbName");

    // Conectar ao banco criado
    $pdo = new PDO("mysql:host=localhost;dbname=$dbName", USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criar a tabela se não existir
    $query = "CREATE TABLE IF NOT EXISTS FORNECEDOR (
        ID INT PRIMARY KEY AUTO_INCREMENT,
        NOME VARCHAR(255) NOT NULL,
        CNPJ VARCHAR(18) UNIQUE NOT NULL
    )";
    $pdo->exec($query);

} catch (PDOException $e) {
    die("ERRO: " . $e->getMessage());
}
?>
