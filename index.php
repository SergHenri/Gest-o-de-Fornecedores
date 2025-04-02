<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Banco de Dados</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

<h2>Tabelas no Banco de Dados</h2>

<?php
define("USER", "root"); // Usuário do banco
define("PASS", "root"); // Senha do banco (padrão do XAMPP é vazio)
define("DBNAME", "DadosFornecedor"); // Nome do seu banco
define("HOST", "localhost"); // Host do banco

try {
    // Conectar ao banco de dados
    $pdo = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buscar todas as tabelas do banco
    $query = $pdo->query("SHOW TABLES");
    $tabelas = $query->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tabelas)) {
        echo "<p><strong>Não há tabelas no banco de dados.</strong></p>";
    } else {
        foreach ($tabelas as $tabela) {
            echo "<h3>Tabela: $tabela</h3>";

            // Buscar os dados de cada tabela
            $stmt = $pdo->query("SELECT * FROM $tabela");
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                echo "<p>Não há registros nesta tabela.</p>";
            } else {
                echo "<table>";
                echo "<tr>";
                // Criar cabeçalho da tabela
                foreach (array_keys($dados[0]) as $coluna) {
                    echo "<th>$coluna</th>";
                }
                echo "</tr>";

                // Inserir os dados na tabela
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

</body>
</html>
