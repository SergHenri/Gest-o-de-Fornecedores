<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Banco de Dados</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        input[type="text"], select { margin-bottom: 10px; padding: 5px; width: 100%; max-width: 300px; display: block; }
        nav a, nav button { margin-right: 10px; }
    </style>
</head>
<body>

<h2>Tabelas no Banco de Dados</h2>

<nav>
    <a href="PageOne.html">Inicial</a>
    <a href="CadastroFornecedor.html">Cadastro de Fornecedores</a>
    <a href="update.html">Editar</a>
</nav>

<?php
define("USER", "root");
define("PASS", "root");
define("DBNAME", "DadosFornecedor");
define("HOST", "localhost");

try {
    $pdo = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $pdo->query("SHOW TABLES");
    $tabelas = $query->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tabelas)) {
        echo "<p><strong>Não há tabelas no banco de dados.</strong></p>";
    } else {
        foreach ($tabelas as $tabela) {
            echo "<h3>Tabela: $tabela</h3>";

            // Obter as colunas da tabela com segurança
            $resultadoColunas = $pdo->query("SELECT * FROM $tabela LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if ($resultadoColunas) {
                $colunas = array_keys($resultadoColunas);
            } else {
                $colunas = []; // Tabela está vazia
            }

            // Filtro por coluna e texto
            if (!empty($colunas)) {
                echo "<label for='coluna_$tabela'>Filtrar por coluna:</label>";
                echo "<select id='coluna_$tabela'>";
                foreach ($colunas as $coluna) {
                    echo "<option value='$coluna'>$coluna</option>";
                }
                echo "</select>";
                echo "<input type='text' onkeyup='filtrarTabela(\"$tabela\")' placeholder='Digite o valor...' id='filtro_$tabela'>";
            }

            $stmt = $pdo->query("SELECT * FROM $tabela");
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                echo "<p>Não há registros nesta tabela.</p>";
            } else {
                echo "<table id='tabela_$tabela'>";
                echo "<tr>";
                foreach ($colunas as $coluna) {
                    echo "<th>$coluna</th>";
                }
                echo "</tr>";

                foreach ($dados as $linha) {
                    echo "<tr>";
                    foreach ($linha as $valor) {
                        echo "<td>$valor</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
    }
} catch (PDOException $e) {
    echo "<p>Erro ao conectar ao banco de dados: " . $e->getMessage() . "</p>";
}
?>

<script>
    function filtrarTabela(nomeTabela) {
        let input = document.getElementById('filtro_' + nomeTabela);
        let filtro = input.value.toLowerCase();
        let colunaSelecionada = document.getElementById('coluna_' + nomeTabela).value;
        let tabela = document.getElementById('tabela_' + nomeTabela);
        let linhas = tabela.getElementsByTagName('tr');
        let headers = tabela.getElementsByTagName('th');

        let colunaIndex = -1;

        // Descobrir o índice da coluna selecionada
        for (let i = 0; i < headers.length; i++) {
            if (headers[i].textContent === colunaSelecionada) {
                colunaIndex = i;
                break;
            }
        }

        if (colunaIndex === -1) return;

        // Começa de 1 para ignorar cabeçalho
        for (let i = 1; i < linhas.length; i++) {
            let colunas = linhas[i].getElementsByTagName('td');
            let celula = colunas[colunaIndex];
            if (celula && celula.textContent.toLowerCase().includes(filtro)) {
                linhas[i].style.display = '';
            } else {
                linhas[i].style.display = 'none';
            }
        }
    }
</script>

</body>
</html>
