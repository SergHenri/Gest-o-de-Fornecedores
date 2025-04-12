<?php
// Define constantes para o nome de usuário e senha de acesso ao banco de dados
define("USER", "root");
define("PASS", "root");

// Tenta realizar a conexão com o banco de dados usando PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=DadosFornecedor", USER, PASS); // Conecta ao banco de dados chamado "DadosFornecedor"
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ativa o modo de erro para lançar exceções em caso de problemas
} catch (PDOException $e) {
    // Caso ocorra erro na conexão, mostra a mensagem e finaliza o script
    die("Erro na conexão: " . $e->getMessage());
}

// Verifica se o formulário foi enviado via método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Coleta os dados enviados via formulário
    $tabela = $_POST['tabela']; // Nome da tabela no banco de dados
    $coluna_deletar = $_POST['coluna_deletar']; // Nome da coluna que será usada como filtro
    $valor_delete = $_POST['valor_delete']; // Valor da coluna a ser usado no filtro
    $acao = $_POST['acao']; // Ação a ser realizada (neste caso, "deletar")

    // Função anônima para validar nomes de tabela e coluna
    // Permite apenas letras, números e underscores (_) — evita comandos maliciosos (SQL Injection)
    $valida_nome = function($str) {
        return preg_match('/^[a-zA-Z0-9_]+$/', $str);
    };

    // Verifica se os nomes da tabela e da coluna são válidos, senão encerra
    if (!$valida_nome($tabela) || !$valida_nome($coluna_deletar)) {
        die("Erro: Nome de tabela ou coluna inválido.");
    }

    // BLOCO RESPONSÁVEL PELA EXCLUSÃO DE REGISTRO
    if ($acao == "deletar") {

        // Confirma novamente os dados da coluna e do valor
        $coluna_deletar = $_POST['coluna_deletar'];
        $valor_delete = $_POST['valor_delete'];

        // Verifica se a coluna foi preenchida e é válida
        if (!$coluna_deletar || !$valida_nome($coluna_deletar)) {
            die("Erro: Coluna para deletar inválida ou não preenchida.");
        }

        // Monta o comando SQL com parâmetros nomeados para evitar SQL Injection
        $sql = "DELETE FROM $tabela WHERE $coluna_deletar = :valor_delete";

        try {
            // Prepara a execução da instrução SQL
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":valor_delete", $valor_delete); // Substitui o parâmetro pelo valor recebido

            // Executa a query e verifica se foi bem-sucedida
            if ($stmt->execute()) {
                echo "Registro excluído com sucesso!";
            } else {
                echo "Erro ao excluir o registro.";
            }
        } catch (PDOException $e) {
            // Captura erros durante a execução da query
            echo "Erro na exclusão: " . $e->getMessage();
        }
    }
}

// Após a execução, exibe dois botões para voltar à página inicial ou cadastrar um novo fornecedor
echo '<div>
<a href="PageOne.html">
    <button>
        Voltar à Página Inicial
    </button>
</a>
<a href="CadastroFornecedor.html">
    <button>
        Cadastrar Novo Fornecedor
    </button>
</a>
</div>';
?>
