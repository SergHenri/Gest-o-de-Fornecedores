<!DOCTYPE html> <!-- Define o tipo do documento como HTML5 -->
<html lang="pt-br"> <!-- Início do HTML com idioma definido como português do Brasil -->
<head>
    <meta charset="UTF-8"> <!-- Define o conjunto de caracteres como UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsividade para dispositivos móveis -->
    <title>Visualizar Banco de Dados</title> <!-- Título da aba do navegador -->
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; } /* Estilo do corpo da página */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; } /* Estilo das tabelas */
        th, td { border: 1px solid black; padding: 8px; text-align: left; } /* Estilo das células */
        th { background-color: #f2f2f2; } /* Cor de fundo do cabeçalho da tabela */
        input[type="text"], select { margin-bottom: 10px; padding: 5px; width: 100%; max-width: 300px; display: block; } /* Estilo de inputs e selects */
        nav a, nav button { margin-right: 10px; } /* Espaçamento entre os itens do menu de navegação */
    </style>
</head>
<body>

<h2>Tabelas no Banco de Dados</h2> <!-- Título principal da página -->

<nav>
    <a href="PageOne.html">Inicial</a> <!-- Link para página inicial -->
    <a href="CadastroFornecedor.html">Cadastro de Fornecedores</a> <!-- Link para cadastro de fornecedores -->
    <a href="update.html">Editar</a> <!-- Link para edição de dados -->
</nav>

<?php
// Definição de constantes para conexão com o banco de dados
define("USER", "root"); // Nome de usuário do banco
define("PASS", "root"); // Senha do banco
define("DBNAME", "DadosFornecedor"); // Nome do banco de dados
define("HOST", "localhost"); // Endereço do host

try {
    // Criação da conexão PDO com o banco de dados
    $pdo = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Define o modo de erro como exceção

    // Consulta para listar todas as tabelas do banco de dados
    $query = $pdo->query("SHOW TABLES");
    $tabelas = $query->fetchAll(PDO::FETCH_COLUMN); // Retorna um array com os nomes das tabelas

    // Verifica se existem tabelas no banco
    if (empty($tabelas)) {
        echo "<p><strong>Não há tabelas no banco de dados.</strong></p>"; // Mensagem caso não existam tabelas
    } else {
        // Loop para percorrer cada tabela encontrada
        foreach ($tabelas as $tabela) {
            echo "<h3>Tabela: $tabela</h3>"; // Mostra o nome da tabela

            // Obtém as colunas da tabela (pega apenas a primeira linha para extrair os nomes)
            $resultadoColunas = $pdo->query("SELECT * FROM $tabela LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if ($resultadoColunas) {
                $colunas = array_keys($resultadoColunas); // Obtém os nomes das colunas
            } else {
                $colunas = []; // Caso não haja colunas
            }

            // Se houver colunas, exibe campos para filtro
            if (!empty($colunas)) {
                echo "<label for='coluna_$tabela'>Filtrar por coluna:</label>"; // Label do select
                echo "<select id='coluna_$tabela'>"; // Início do select para escolher a coluna
                foreach ($colunas as $coluna) {
                    echo "<option value='$coluna'>$coluna</option>"; // Opções com os nomes das colunas
                }
                echo "</select>"; // Fim do select
                echo "<input type='text' onkeyup='filtrarTabela(\"$tabela\")' placeholder='Digite o valor...' id='filtro_$tabela'>"; // Campo de texto para digitar o valor do filtro
            }

            // Consulta todos os dados da tabela
            $stmt = $pdo->query("SELECT * FROM $tabela");
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC); // Armazena os dados em array associativo

            // Verifica se há dados na tabela
            if (empty($dados)) {
                echo "<p>Não há registros nesta tabela.</p>"; // Mensagem se não houver registros
            } else {
                // Monta a tabela HTML com os dados
                echo "<table id='tabela_$tabela'>"; // Início da tabela
                echo "<tr>";
                foreach ($colunas as $coluna) {
                    echo "<th>$coluna</th>"; // Cabeçalhos da tabela
                }
                echo "</tr>";

                // Exibe as linhas de dados
                foreach ($dados as $linha) {
                    echo "<tr>";
                    foreach ($linha as $valor) {
                        echo "<td>$valor</td>"; // Células da linha
                    }
                    echo "</tr>";
                }
                echo "</table>"; // Fim da tabela
            }
        }
    }
} catch (PDOException $e) {
    // Exibe mensagem de erro em caso de falha na conexão
    echo "<p>Erro ao conectar ao banco de dados: " . $e->getMessage() . "</p>";
}
?>

<script>
    // Função para filtrar os dados da tabela com base em uma coluna e valor digitado
    function filtrarTabela(nomeTabela) {
        let input = document.getElementById('filtro_' + nomeTabela); // Obtém o campo de texto do filtro
        let filtro = input.value.toLowerCase(); // Converte o valor digitado para minúsculo
        let colunaSelecionada = document.getElementById('coluna_' + nomeTabela).value; // Obtém a coluna selecionada
        let tabela = document.getElementById('tabela_' + nomeTabela); // Obtém a tabela HTML
        let linhas = tabela.getElementsByTagName('tr'); // Obtém todas as linhas da tabela
        let headers = tabela.getElementsByTagName('th'); // Obtém os cabeçalhos da tabela

        let colunaIndex = -1; // Inicializa o índice da coluna

        // Encontra o índice da coluna selecionada
        for (let i = 0; i < headers.length; i++) {
            if (headers[i].textContent === colunaSelecionada) {
                colunaIndex = i;
                break;
            }
        }

        if (colunaIndex === -1) return; // Se a coluna não for encontrada, sai da função

        // Itera sobre as linhas da tabela (começa do índice 1 para ignorar o cabeçalho)
        for (let i = 1; i < linhas.length; i++) {
            let colunas = linhas[i].getElementsByTagName('td'); // Obtém as células da linha
            let celula = colunas[colunaIndex]; // Obtém a célula da coluna selecionada

            // Verifica se a célula contém o valor digitado (ignora maiúsculas/minúsculas)
            if (celula && celula.textContent.toLowerCase().includes(filtro)) {
                linhas[i].style.display = ''; // Exibe a linha
            } else {
                linhas[i].style.display = 'none'; // Oculta a linha
            }
        }
    }
</script>
