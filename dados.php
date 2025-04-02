<?php
try {
    // Conexão com o servidor MySQL (sem especificar o banco de dados)
    $pdo = new PDO("mysql:host=localhost", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criação do banco de dados FORNECEDORES, se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS FORNECEDORES");

    // Conexão com o banco de dados FORNECEDORES
    $pdo = new PDO("mysql:host=localhost;dbname=FORNECEDORES", "root", "root");
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

$criarTabelaEndereco = "CREATE TABLE IF NOT EXISTS ENDERECO (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDFORNECEDOR INT NOT NULL,
    LOGRADOURO VARCHAR(255) NOT NULL,
    NUMERO VARCHAR(10) NOT NULL,
    COMPLEMENTO VARCHAR(255) NULL,
    CEP VARCHAR(9) NOT NULL,
    BAIRRO VARCHAR(255),
    CIDADE VARCHAR(255) NOT NULL,
    UF CHAR(2) NOT NULL,
    FOREIGN KEY (FK_IDFORNECEDOR) REFERENCES FORNECEDOR(ID)
)";

$criarTabelaTelefone = "CREATE TABLE IF NOT EXISTS TELEFONE (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDFORNECEDOR INT NOT NULL,
    PRINCIPAL VARCHAR(20),
    TELEFONE VARCHAR(20),
    CELULAR VARCHAR(20),
    FOREIGN KEY (FK_IDFORNECEDOR) REFERENCES FORNECEDOR(ID)
)";

$criarTabelaContratos = "CREATE TABLE IF NOT EXISTS CONTRATOS (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDFORNECEDOR INT NOT NULL,
    FK_IDSERVICOS INT NOT NULL,
    DATA_INICIAL DATE NOT NULL,
    DATA_FINAL DATE NOT NULL,
    DURACAO INT,
    FOREIGN KEY (FK_IDFORNECEDOR) REFERENCES FORNECEDOR(ID),
    FOREIGN KEY (FK_IDSERVICOS) REFERENCES SERVICOS(ID)
)";

$criarTabelaClienteForcedor = "CREATE TABLE IF NOT EXISTS CLIENTES_FORNECEDOR (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDFORNECEDOR INT NOT NULL,
    SERVICO VARCHAR(255),
    CLIENTE VARCHAR(255),
    FOREIGN KEY (FK_IDFORNECEDOR) REFERENCES FORNECEDOR(ID)
)";

// Criação das tabelas (se necessário)
verificarOuCriarTabela($pdo, $criarTabelaFornecedor);
verificarOuCriarTabela($pdo, $criarTabelaServicos);
verificarOuCriarTabela($pdo, $criarTabelaEndereco);
verificarOuCriarTabela($pdo, $criarTabelaTelefone);
verificarOuCriarTabela($pdo, $criarTabelaContratos);
verificarOuCriarTabela($pdo, $criarTabelaClienteForcedor);

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

        // Inserir dados na tabela ENDERECO
        $stmt = $pdo->prepare("INSERT INTO ENDERECO (FK_IDFORNECEDOR, LOGRADOURO, NUMERO, COMPLEMENTO, CEP, BAIRRO, CIDADE, UF) VALUES (:fk_idfornecedor, :logradouro, :numero, :complemento, :cep, :bairro, :cidade, :uf)");
        $stmt->execute(['fk_idfornecedor' => $fornecedorId, 'logradouro' => $_POST['logradouro'], 'numero' => $_POST['numero'], 'complemento' => $_POST['complemento'], 'cep' => $_POST['cep'], 'bairro' => $_POST['bairro'], 'cidade' => $_POST['cidade'], 'uf' => $_POST['uf']]);

        // Inserir dados na tabela TELEFONE
        $stmt = $pdo->prepare("INSERT INTO TELEFONE (FK_IDFORNECEDOR, PRINCIPAL, TELEFONE, CELULAR) VALUES (:fk_idfornecedor, :principal, :telefone, :celular)");
        $stmt->execute(['fk_idfornecedor' => $fornecedorId, 'principal' => $_POST['telefone_principal'], 'telefone' => $_POST['telefone'], 'celular' => $_POST['celular']]);

        // Inserir dados na tabela CONTRATOS
        $stmt = $pdo->prepare("INSERT INTO CONTRATOS (FK_IDFORNECEDOR, FK_IDSERVICOS, DATA_INICIAL, DATA_FINAL, DURACAO) VALUES (:fk_idfornecedor, :fk_idservicos, :data_inicial, :data_final, :duracao)");
        $stmt->execute(['fk_idfornecedor' => $fornecedorId, 'fk_idservicos' => $servicoId, 'data_inicial' => $_POST['data_inicial'], 'data_final' => $_POST['data_final'], 'duracao' => $_POST['duracao']]);

        // Inserir dados na tabela CLIENTES_FORNECEDOR
        $stmt = $pdo->prepare("INSERT INTO CLIENTES_FORNECEDOR (FK_IDFORNECEDOR, SERVICO, CLIENTE) VALUES (:fk_idfornecedor, :servico, :cliente)");
        $stmt->execute(['fk_idfornecedor' => $fornecedorId, 'servico' => $_POST['servico_cliente'], 'cliente' => $_POST['nome_cliente']]);

        $pdo->commit();
        echo "Dados inseridos com sucesso!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Erro ao inserir dados: " . $e->getMessage();
    }
}
?>