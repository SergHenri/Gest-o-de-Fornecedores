<?php
try {
    // Conexão com o servidor MySQL (sem especificar o banco de dados)
    $pdo = new PDO("mysql:host=localhost", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criação do banco de dados DadosFornecedor, se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS DadosFornecedor");

    // Conexão com o banco de dados DadosFornecedor
    $pdo = new PDO("mysql:host=localhost;dbname=DadosFornecedor", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão ou criação do banco de dados: " . $e->getMessage());
}

// Função para verificar e criar tabelas se não existirem (seu código original)
function verificarOuCriarTabela($pdo, $sql) {
    try {
        $pdo->exec($sql);
    } catch (PDOException $e) {
        die("Erro ao criar tabela: " . $e->getMessage());
    }
}

// SQL para criação das tabelas caso não existam
$criarTabelaFornecedor = "CREATE TABLE IF NOT EXISTS FORNECEDOR (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NOME VARCHAR(255) NOT NULL,
    CNPJ VARCHAR(18) UNIQUE NOT NULL
)";

$criarTabelaServicos = "CREATE TABLE IF NOT EXISTS SERVICOS (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDFORNECEDOR INT NOT NULL,
    SERVICO VARCHAR(255) NOT NULL,
    DESCRICAO TEXT,
    FOREIGN KEY (FK_IDFORNECEDOR) REFERENCES FORNECEDOR(ID)
)";


// Criação das tabelas (se necessário)
verificarOuCriarTabela($pdo, $criarTabelaFornecedor);
verificarOuCriarTabela($pdo, $criarTabelaServicos);


// Inserção de dados do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo->beginTransaction();

        // Inserir dados na tabela FORNECEDOR
        $stmt = $pdo->prepare("INSERT INTO FORNECEDOR (NOME, CNPJ) VALUES (:nome, :cnpj)");
        $stmt->execute(['nome' => $_POST['nome'], 'cnpj' => $_POST['cnpj']]);
        $fornecedorId = $pdo->lastInsertId();

        // Inserir dados na tabela SERVICOS
        $stmt = $pdo->prepare("INSERT INTO SERVICOS (FK_IDFORNECEDOR, SERVICO, DESCRICAO) VALUES (:fk_idfornecedor, :servico, :descricao)");
        $stmt->execute(['fk_idfornecedor' => $fornecedorId, 'servico' => $_POST['servico'], 'descricao' => $_POST['descricao']]);
        $servicoId = $pdo->lastInsertId();

        $pdo->commit();
        echo "Dados inseridos com sucesso!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Erro ao inserir dados: " . $e->getMessage();
    }
}
?>