<?php

// Define constantes de usuário e senha para conexão com o banco de dados
define("USER", "root");
define("PASS", "root");

try {
    // Conexão inicial com o servidor MySQL (sem selecionar banco ainda)
    $pdo = new PDO("mysql:host=localhost", USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cria o banco de dados 'DadosFornecedor' caso ainda não exista
    $pdo->exec("CREATE DATABASE IF NOT EXISTS DadosFornecedor");

    // Conecta agora ao banco de dados recém-criado ou já existente
    $pdo = new PDO("mysql:host=localhost;dbname=DadosFornecedor", USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Em caso de erro de conexão ou criação do banco, exibe mensagem
    die("Erro na conexão ou criação do banco de dados: " . $e->getMessage());
}

// Função responsável por verificar e criar uma tabela com base no SQL fornecido
function verificarOuCriarTabela($pdo, $sql) {
    try {
        $pdo->exec($sql); // Executa o comando de criação da tabela
    } catch (PDOException $e) {
        die("Erro ao criar tabela: " . $e->getMessage()); // Exibe erro, se houver
    }
}

// SQL de criação da tabela FORNECEDOR
$criarTabelaFornecedor = "CREATE TABLE IF NOT EXISTS FORNECEDOR (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NOME VARCHAR(255) NOT NULL,
    CNPJ VARCHAR(18) UNIQUE NOT NULL
)";

// SQL de criação da tabela SERVIÇOS (com chave estrangeira para FORNECEDOR)
$criarTabelaServicos = "CREATE TABLE IF NOT EXISTS SERVICOS (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDFORNECEDOR INT NOT NULL,
    SERVICO VARCHAR(255) NOT NULL,
    DESCRICAO TEXT,
    FOREIGN KEY (FK_IDFORNECEDOR) REFERENCES FORNECEDOR(ID)
)";

// SQL de criação da tabela ENDEREÇO (com chave estrangeira para FORNECEDOR)
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

// SQL de criação da tabela TELEFONE (com chave estrangeira para FORNECEDOR)
$criarTabelaTelefone = "CREATE TABLE IF NOT EXISTS TELEFONE (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDFORNECEDOR INT NOT NULL,
    PRINCIPAL VARCHAR(20),
    TELEFONE VARCHAR(20),
    CELULAR VARCHAR(20),
    FOREIGN KEY (FK_IDFORNECEDOR) REFERENCES FORNECEDOR(ID)
)";

// SQL de criação da tabela CONTRATOS (com chaves estrangeiras para FORNECEDOR e SERVIÇOS)
$criarTabelaContratos = "CREATE TABLE IF NOT EXISTS CONTRATOS (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDFORNECEDOR INT NOT NULL,
    FK_IDSERVICOS INT NOT NULL,
    DATA_INICIAL DATE NOT NULL,
    DATA_FINAL DATE NOT NULL,
    FOREIGN KEY (FK_IDFORNECEDOR) REFERENCES FORNECEDOR(ID),
    FOREIGN KEY (FK_IDSERVICOS) REFERENCES SERVICOS(ID)
)";

// SQL de criação da tabela CLIENTES_FORNECEDOR (relaciona clientes a fornecedores e serviços)
$criarTabelaClienteForcedor = "CREATE TABLE IF NOT EXISTS CLIENTES_FORNECEDOR (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDFORNECEDOR INT NOT NULL,
    SERVICO VARCHAR(255),
    CLIENTE VARCHAR(255),
    FOREIGN KEY (FK_IDFORNECEDOR) REFERENCES FORNECEDOR(ID)
)";

// Cria todas as tabelas no banco de dados, se ainda não existirem
verificarOuCriarTabela($pdo, $criarTabelaFornecedor);
verificarOuCriarTabela($pdo, $criarTabelaServicos);
verificarOuCriarTabela($pdo, $criarTabelaEndereco);
verificarOuCriarTabela($pdo, $criarTabelaTelefone);
verificarOuCriarTabela($pdo, $criarTabelaContratos);
verificarOuCriarTabela($pdo, $criarTabelaClienteForcedor);

// Verifica se o formulário foi enviado via método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Inicia uma transação para garantir que todas as inserções sejam feitas com sucesso
        $pdo->beginTransaction();

        // Insere os dados do fornecedor na tabela FORNECEDOR
        $stmt = $pdo->prepare("INSERT INTO FORNECEDOR (NOME, CNPJ) VALUES (:nome, :cnpj)");
        $stmt->execute([
            'nome' => $_POST['nome'],
            'cnpj' => $_POST['cnpj']
        ]);
        $fornecedorId = $pdo->lastInsertId(); // Recupera o ID do fornecedor recém-inserido

        // Insere os dados do serviço relacionado ao fornecedor
        $stmt = $pdo->prepare("INSERT INTO SERVICOS (FK_IDFORNECEDOR, SERVICO, DESCRICAO) VALUES (:fk_idfornecedor, :servico, :descricao)");
        $stmt->execute([
            'fk_idfornecedor' => $fornecedorId,
            'servico' => $_POST['servico'],
            'descricao' => $_POST['descricao']
        ]);
        $servicoId = $pdo->lastInsertId(); // Recupera o ID do serviço

        // Insere os dados de endereço do fornecedor
        $stmt = $pdo->prepare("INSERT INTO ENDERECO (FK_IDFORNECEDOR, LOGRADOURO, NUMERO, COMPLEMENTO, CEP, BAIRRO, CIDADE, UF)
                               VALUES (:fk_idfornecedor, :logradouro, :numero, :complemento, :cep, :bairro, :cidade, :uf)");
        $stmt->execute([
            'fk_idfornecedor' => $fornecedorId,
            'logradouro' => $_POST['logradouro'],
            'numero' => $_POST['numero'],
            'complemento' => $_POST['complemento'],
            'cep' => $_POST['cep'],
            'bairro' => $_POST['bairro'],
            'cidade' => $_POST['cidade'],
            'uf' => $_POST['uf']
        ]);

        // Insere os dados de telefone
        $stmt = $pdo->prepare("INSERT INTO TELEFONE (FK_IDFORNECEDOR, PRINCIPAL, TELEFONE, CELULAR) 
                               VALUES (:fk_idfornecedor, :principal, :telefone, :celular)");
        $stmt->execute([
            'fk_idfornecedor' => $fornecedorId,
            'principal' => $_POST['principal'],
            'telefone' => $_POST['telefone'],
            'celular' => $_POST['celular']
        ]);

        // Insere os dados do contrato com datas
        $stmt = $pdo->prepare("INSERT INTO CONTRATOS (FK_IDFORNECEDOR, FK_IDSERVICOS, DATA_INICIAL, DATA_FINAL)
                               VALUES (:fk_idfornecedor, :fk_idservicos, :data_inicial, :data_final)");
        $stmt->execute([
            'fk_idfornecedor' => $fornecedorId,
            'fk_idservicos' => $servicoId,
            'data_inicial' => $_POST['data_inicial'],
            'data_final' => $_POST['data_final']
        ]);

        // Insere os dados do cliente vinculado ao fornecedor
        $stmt = $pdo->prepare("INSERT INTO CLIENTES_FORNECEDOR (FK_IDFORNECEDOR, SERVICO, CLIENTE) 
                               VALUES (:fk_idfornecedor, :servico, :cliente)");
        $stmt->execute([
            'fk_idfornecedor' => $fornecedorId,
            'servico' => $_POST['servico'],
            'cliente' => $_POST['cliente']
        ]);

        // Finaliza a transação com sucesso
        $pdo->commit();
        echo "Dados inseridos com sucesso!";

        // Exibe botões de navegação após cadastro
        echo '<div style="margin-top: 20px;">
        <a href="PageOne.html">
            <button style="padding: 10px 20px; margin-right: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Voltar à Página Inicial
            </button>
        </a>
        <a href="CadastroFornecedor.html">
            <button style="padding: 10px 20px; background-color: #008CBA; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Cadastrar Novo Fornecedor
            </button>
        </a>
    </div>';
    } catch (PDOException $e) {
        // Em caso de erro, desfaz a transação e exibe a mensagem
        $pdo->rollBack();
        echo "Erro ao inserir dados: " . $e->getMessage();
    }
}
?>
