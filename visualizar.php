<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

$usuarioLogado = $_SESSION['usuario'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=DadosFornecedor", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Função para buscar dados filtrando pelo usuário
function buscarDados($pdo, $tabela, $usuarioLogado) {
    $stmt = $pdo->prepare("SELECT * FROM $tabela WHERE USUARIO = :usuario");
    $stmt->bindParam(':usuario', $usuarioLogado);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Busca dados das tabelas
$clientes = buscarDados($pdo, "clientes_fornecedor", $usuarioLogado);
$contratos = buscarDados($pdo, "contratos", $usuarioLogado);
$endereco = buscarDados($pdo, "endereco", $usuarioLogado);
$fornecedor = buscarDados($pdo, "fornecedor", $usuarioLogado);
$servicos = buscarDados($pdo, "servicos", $usuarioLogado);
$telefone = buscarDados($pdo, "telefone", $usuarioLogado);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dados do Usuário</title>
</head>
<body>
    <h2>Bem-vindo, <?= htmlspecialchars($usuarioLogado) ?></h2>

    <h3>Clientes Fornecedor</h3>
    <table border="3">
        <tr><th>ID</th><th>USUARIO</th><th>FK_IDFORNECEDOR</th><th>SERVICO</th><th>CLIENTE</th></tr>
        <?php foreach ($clientes as $c): ?>
        <tr>
            <td><?= $c['ID'] ?></td>
            <td><?= $c['USUARIO'] ?></td>
            <td><?= $c['FK_IDFORNECEDOR'] ?></td>
            <td><?= $c['SERVICO'] ?></td>
            <td><?= $c['CLIENTE'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Contratos</h3>
    <table border="3">
        <tr><th>ID</th><th>USUARIO</th><th>FK_IDFORNECEDOR</th><th>FK_IDSERVICOS</th><th>DATA_INICIAL</th><th>DATA_FINAL</th></tr>
        <?php foreach ($contratos as $c): ?>
        <tr>
            <td><?= $c['ID'] ?></td>
            <td><?= $c['USUARIO'] ?></td>
            <td><?= $c['FK_IDFORNECEDOR'] ?></td>
            <td><?= $c['FK_IDSERVICOS'] ?></td>
            <td><?= $c['DATA_INICIAL'] ?></td>
            <td><?= $c['DATA_FINAL'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Endereço</h3>
    <table border="3">
        <tr><th>ID</th><th>USUARIO</th><th>LOGRADOURO</th><th>NÚMERO</th><th>COMPLEMENTO</th><th>CEP</th><th>BAIRRO</th><th>CIDADE</th><th>UF</th></tr>
        <?php foreach ($endereco as $e): ?>
        <tr>
            <td><?= $e['ID'] ?></td>
            <td><?= $e['USUARIO'] ?></td>
            <td><?= $e['LOGRADOURO'] ?></td>
            <td><?= $e['NUMERO'] ?></td>
            <td><?= $e['COMPLEMENTO'] ?></td>
            <td><?= $e['CEP'] ?></td>
            <td><?= $e['BAIRRO'] ?></td>
            <td><?= $e['CIDADE'] ?></td>
            <td><?= $e['UF'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Fornecedor</h3>
    <table border="3">
        <tr><th>ID</th><th>USUARIO</th><th>NOME</th><th>CNPJ</th><th>EMAIL</th></tr>
        <?php foreach ($fornecedor as $f): ?>
        <tr>
            <td><?= $f['ID'] ?></td>
            <td><?= $f['USUARIO'] ?></td>
            <td><?= $f['NOME'] ?></td>
            <td><?= $f['CNPJ'] ?></td>
            <td><?= $f['EMAIL'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Serviços</h3>
    <table border="3">
        <tr><th>ID</th><th>USUARIO</th><th>FK_IDFORNECEDOR</th><th>SERVICO</th><th>DESCRICAO</th></tr>
        <?php foreach ($servicos as $s): ?>
        <tr>
            <td><?= $s['ID'] ?></td>
            <td><?= $s['USUARIO'] ?></td>
            <td><?= $s['FK_IDFORNECEDOR'] ?></td>
            <td><?= $s['SERVICO'] ?></td>
            <td><?= $s['DESCRICAO'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Telefone</h3>
    <table border="3">
        <tr><th>ID</th><th>USUARIO</th><th>FK_IDFORNECEDOR</th><th>PRINCIPAL</th><th>TELEFONE</th><th>CELULAR</th></tr>
        <?php foreach ($telefone as $t): ?>
        <tr>
            <td><?= $t['ID'] ?></td>
            <td><?= $t['USUARIO'] ?></td>
            <td><?= $t['FK_IDFORNECEDOR'] ?></td>
            <td><?= $t['PRINCIPAL'] ?></td>
            <td><?= $t['TELEFONE'] ?></td>
            <td><?= $t['CELULAR'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <br> <!-- Quebra de linha -->
    <br> <!-- Outra quebra de linha -->

    <nav class=""> <!-- Menu de navegação (classe vazia) -->
        <a href="logout.php"> <!-- Link para o logout -->
            <button>
                Logout
            </button> <!-- Botão para sair do sistema -->
        </a>
    </nav>

    
</body>
</html>
