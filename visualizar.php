<?php
session_start();

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

function buscarDados($pdo, $tabela, $usuarioLogado) {
    if ($tabela == 'contratos') {
        $stmt = $pdo->prepare("SELECT * FROM $tabela WHERE USUARIO = :usuario");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM $tabela WHERE FK_IDCONTRATOS = :usuario");
    }
    $stmt->bindParam(':usuario', $usuarioLogado);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$clientes = buscarDados($pdo, "clientes_fornecedor", $usuarioLogado);
$contratos = buscarDados($pdo, "contratos", $usuarioLogado);
$endereco = buscarDados($pdo, "endereco", $usuarioLogado);
$servicos = buscarDados($pdo, "servicos", $usuarioLogado);
$telefone = buscarDados($pdo, "telefone", $usuarioLogado);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dados do Usuário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f4f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h2, h3 {
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        table, th, td {
            border: 1px solid #dcdcdc;
        }

        th {
            background-color: #3498db;
            color: white;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 10px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        button {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #c0392b;
        }

        nav {
            margin-top: 20px;
        }

        a {
            text-decoration: none;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                position: sticky;
                top: 0;
                z-index: 2;
            }

            td {
                border: none;
                border-bottom: 1px solid #ddd;
                position: relative;
                padding-left: 50%;
            }

            td::before {
                position: absolute;
                top: 10px;
                left: 10px;
                white-space: nowrap;
                font-weight: bold;
                content: attr(data-label);
            }
        }
    </style>
</head>
<body>
    <h2>Bem-vindo, <?= htmlspecialchars($usuarioLogado) ?></h2>

    <h3>Contratos</h3>
    <table>
        <tr><th>ID</th><th>USUARIO</th><th>NOME</th><th>CNPJ</th><th>EMAIL</th><th>DATA_INICIAL</th><th>DATA_FINAL</th>
        <?php foreach ($contratos as $c): ?>
        <tr>
            <td><?= $c['ID'] ?></td>
            <td><?= $c['USUARIO'] ?></td>
            <td><?= $c['NOME'] ?></td>
            <td><?= $c['CNPJ'] ?></td>
            <td><?= $c['EMAIL'] ?></td>
            <td><?= $c['DATA_INICIAL'] ?></td>
            <td><?= $c['DATA_FINAL'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Serviços</h3>
    <table>
        <tr><th>ID</th><th>USUARIO</th><th>SERVICO</th><th>DESCRICAO</th></tr>
        <?php foreach ($servicos as $s): ?>
        <tr>
            <td><?= $s['ID'] ?></td>
            <td><?= $s['FK_IDCONTRATOS'] ?></td>
            <td><?= $s['SERVICO'] ?></td>
            <td><?= $s['DESCRICAO'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Endereço</h3>
    <table>
        <tr><th>ID</th><th>USUARIO</th><th>LOGRADOURO</th><th>NÚMERO</th><th>COMPLEMENTO</th><th>CEP</th><th>BAIRRO</th><th>CIDADE</th><th>UF</th></tr>
        <?php foreach ($endereco as $e): ?>
        <tr>
            <td><?= $e['ID'] ?></td>
            <td><?= $e['FK_IDCONTRATOS'] ?></td>
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

    <h3>Telefone</h3>
    <table>
        <tr><th>ID</th><th>USUARIO</th><th>PRINCIPAL</th><th>TELEFONE</th><th>CELULAR</th></tr>
        <?php foreach ($telefone as $t): ?>
        <tr>
            <td><?= $t['ID'] ?></td>
            <td><?= $t['FK_IDCONTRATOS'] ?></td>
            <td><?= $t['PRINCIPAL'] ?></td>
            <td><?= $t['TELEFONE'] ?></td>
            <td><?= $t['CELULAR'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Clientes Fornecedor</h3>
    <table>
        <tr><th>ID</th><th>USUARIO</th><th>SERVICO</th><th>CLIENTE</th></tr>
        <?php foreach ($clientes as $c): ?>
        <tr>
            <td><?= $c['ID'] ?></td>
            <td><?= $c['FK_IDCONTRATOS'] ?></td>
            <td><?= $c['SERVICO'] ?></td>
            <td><?= $c['CLIENTE'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <nav>
        <a href="logout.php">
            <button>Logout</button>
        </a>
    </nav>
</body>
</html>
