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

// SQL de criação da tabela CONTRATOS (com chaves estrangeiras para FORNECEDOR e SERVIÇOS)
$criarTabelaContratos = "CREATE TABLE IF NOT EXISTS CONTRATOS (
    ID INT AUTO_INCREMENT,
    USUARIO VARCHAR(10) NOT NULL UNIQUE,
    NOME VARCHAR(200) NOT NULL,
    CNPJ VARCHAR(20) NOT NULL,
    EMAIL VARCHAR(200) NOT NULL,
    DATA_INICIAL DATE NOT NULL,
    DATA_FINAL DATE NOT NULL,
    PRIMARY KEY (ID, USUARIO)
)";


// SQL de criação da tabela SERVIÇOS (com chave estrangeira para FORNECEDOR)
$criarTabelaServicos = "CREATE TABLE IF NOT EXISTS SERVICOS (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDCONTRATOS VARCHAR(10) NOT NULL,
    SERVICO VARCHAR(255) NOT NULL,
    DESCRICAO TEXT,
    FOREIGN KEY (FK_IDCONTRATOS) REFERENCES CONTRATOS(USUARIO)
)";

// SQL de criação da tabela ENDEREÇO (com chave estrangeira para FORNECEDOR)
$criarTabelaEndereco = "CREATE TABLE IF NOT EXISTS ENDERECO (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDCONTRATOS VARCHAR(10) NOT NULL,
    LOGRADOURO VARCHAR(255) NOT NULL,
    NUMERO VARCHAR(10) NOT NULL,
    COMPLEMENTO VARCHAR(255) NULL,
    CEP VARCHAR(9) NOT NULL,
    BAIRRO VARCHAR(255),
    CIDADE VARCHAR(255) NOT NULL,
    UF CHAR(2) NOT NULL,
    FOREIGN KEY (FK_IDCONTRATOS) REFERENCES CONTRATOS(USUARIO)
)";

// SQL de criação da tabela TELEFONE (com chave estrangeira para FORNECEDOR)
$criarTabelaTelefone = "CREATE TABLE IF NOT EXISTS TELEFONE (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDCONTRATOS VARCHAR(10) NOT NULL,
    PRINCIPAL VARCHAR(20),
    TELEFONE VARCHAR(20),
    CELULAR VARCHAR(20),
    FOREIGN KEY (FK_IDCONTRATOS) REFERENCES CONTRATOS(USUARIO)
)";


// SQL de criação da tabela CLIENTES_FORNECEDOR (relaciona clientes a fornecedores e serviços)
$criarTabelaClienteForcedor = "CREATE TABLE IF NOT EXISTS CLIENTES_FORNECEDOR (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    FK_IDCONTRATOS VARCHAR(10) NOT NULL,
    SERVICO VARCHAR(255),
    CLIENTE VARCHAR(255),
    FOREIGN KEY (FK_IDCONTRATOS) REFERENCES CONTRATOS(USUARIO)
)";

// Cria todas as tabelas no banco de dados, se ainda não existirem
verificarOuCriarTabela($pdo, $criarTabelaContratos);
verificarOuCriarTabela($pdo, $criarTabelaServicos);
verificarOuCriarTabela($pdo, $criarTabelaEndereco);
verificarOuCriarTabela($pdo, $criarTabelaTelefone);
verificarOuCriarTabela($pdo, $criarTabelaClienteForcedor);

// Verifica se o formulário foi enviado via método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Inicia uma transação para garantir que todas as inserções sejam feitas com sucesso
        $pdo->beginTransaction();

        $usuario = $_POST['user'];

        // Insere os dados do contrato com datas
        $stmt = $pdo->prepare("INSERT INTO CONTRATOS (USUARIO, NOME, CNPJ, EMAIL, DATA_INICIAL, DATA_FINAL)
                               VALUES (:user, :nome, :cnpj, :email, :data_inicial, :data_final)");
        $stmt->execute([
            'user' => $usuario,
            'nome' => $_POST['nome'],
            'cnpj' => $_POST['cnpj'],
            'email' => $_POST['email'],
            'data_inicial' => $_POST['data_inicial'],
            'data_final' => $_POST['data_final']
        ]);
        // $fornecedorId = $usuario; // Recupera o ID do fornecedor recém-inserido

        // Insere os dados do serviço relacionado ao fornecedor
        $stmt = $pdo->prepare("INSERT INTO SERVICOS (FK_IDCONTRATOS, SERVICO, DESCRICAO) VALUES (:fk_idcontratos, :servico, :descricao)");
        $stmt->execute([
            'fk_idcontratos' => $usuario,
            'servico' => $_POST['servico'],
            'descricao' => $_POST['descricao']
        ]);
        $servicoId = $pdo->lastInsertId(); // Recupera o ID do serviço

        // Insere os dados de endereço do fornecedor
        $stmt = $pdo->prepare("INSERT INTO ENDERECO (FK_IDCONTRATOS, LOGRADOURO, NUMERO, COMPLEMENTO, CEP, BAIRRO, CIDADE, UF)
                               VALUES (:fk_idcontratos, :logradouro, :numero, :complemento, :cep, :bairro, :cidade, :uf)");
        $stmt->execute([
            'fk_idcontratos' => $usuario,
            'logradouro' => $_POST['logradouro'],
            'numero' => $_POST['numero'],
            'complemento' => $_POST['complemento'],
            'cep' => $_POST['cep'],
            'bairro' => $_POST['bairro'],
            'cidade' => $_POST['cidade'],
            'uf' => $_POST['uf']
        ]);

        // Insere os dados de telefone
        $stmt = $pdo->prepare("INSERT INTO TELEFONE (FK_IDCONTRATOS, PRINCIPAL, TELEFONE, CELULAR) 
                               VALUES (:fk_idcontratos, :principal, :telefone, :celular)");
        $stmt->execute([
            'fk_idcontratos' => $usuario,
            'principal' => $_POST['principal'],
            'telefone' => $_POST['telefone'],
            'celular' => $_POST['celular']
        ]);

        // Insere os dados do cliente vinculado ao fornecedor
        $stmt = $pdo->prepare("INSERT INTO CLIENTES_FORNECEDOR (FK_IDCONTRATOS, SERVICO, CLIENTE) 
                               VALUES (:fk_idcontratos, :servico, :cliente)");
        $stmt->execute([
            ':fk_idcontratos' => $usuario,
            'servico' => $_POST['servico'],
            'cliente' => $_POST['cliente']
        ]);

        // Finaliza a transação com sucesso
        $pdo->commit();
        echo '<h3 style="text-align: center; margin-top: 20px;" > Dados inseridos com sucesso! </h3>';

        // Exibe botões de navegação após cadastro
        echo '<div "PASS" style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
        <a href="PageOne.html" style="text-decoration: none;">
            <button style="
            background: #1976d2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        " 
        onmouseover="this.style.background=\'#1565c0\'" 
        onmouseout="this.style.background=\'#1976d2\'">
                Voltar à Página Inicial
            </button>
        </a>
        <a href="CadastroFornecedor.html" style="text-decoration: none;">
            <button style="
            background: #1976d2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        " 
        onmouseover="this.style.background=\'#1565c0\'" 
        onmouseout="this.style.background=\'#1976d2\'">
                Cadastrar Novo Fornecedor
            </button>
        </a>
    </div>';
    } catch (PDOException $e) {
        // Em caso de erro, desfaz a transação e exibe a mensagem
        $pdo->rollBack();
        echo '<h3 style="text-align: center; margin-top: 20px; font-family: &quot;Segoe UI&quot;, Tahoma, Geneva, Verdana, sans-serif;">
        Erro ao inserir dados: ' . $e->getMessage() . '</h3>';
    }
}
?>


<style>l{
    font-faamily: "Sin"
}</style>