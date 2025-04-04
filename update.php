<?php
// Define constantes para usuário e senha do banco de dados
define("USER", "root");
define("PASS", "root");

// Tenta conectar ao banco de dados usando PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=DadosFornecedor", USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ativa o modo de erro com exceções
} catch (PDOException $e) {
    // Se a conexão falhar, exibe a mensagem de erro e encerra o script
    die("Erro na conexão: " . $e->getMessage());
}

// Verifica se a requisição foi feita via método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os valores enviados pelo formulário
    $tabela = $_POST['tabela'];
    $coluna_filtro = $_POST['coluna_filtro'];
    $valor_filtro = $_POST['valor_filtro'];
    $acao = $_POST['acao']; // Pode ser "atualizar" ou "deletar"

    // Função anônima para validar se o nome da tabela ou coluna é seguro (evita SQL Injection)
    $valida_nome = function($str) {
        return preg_match('/^[a-zA-Z0-9_]+$/', $str); // Apenas letras, números e underscore
    };

    // Se a tabela ou a coluna do filtro forem inválidas, o script para
    if (!$valida_nome($tabela) || !$valida_nome($coluna_filtro)) {
        die("Erro: Nome de tabela ou coluna inválido.");
    }

    // BLOCO DE ATUALIZAÇÃO
    if ($acao == "atualizar") {
        $coluna_alterar = $_POST['coluna_alterar'];
        $novo_valor = $_POST['novo_valor'];

        // Verifica se o nome da coluna a alterar é válido
        if (!$coluna_alterar || !$valida_nome($coluna_alterar)) {
            die("Erro: Coluna para alterar inválida ou não preenchida.");
        }

        // Monta a query SQL dinamicamente usando parâmetros nomeados
        $sql = "UPDATE $tabela SET $coluna_alterar = :novo_valor WHERE $coluna_filtro = :valor_filtro";

        try {
            $stmt = $pdo->prepare($sql); // Prepara a query para evitar SQL Injection
            $stmt->bindValue(":novo_valor", $novo_valor); // Substitui o placeholder pelo valor real
            $stmt->bindValue(":valor_filtro", $valor_filtro);

            if ($stmt->execute()) {
                echo "Registro atualizado com sucesso!";
            } else {
                echo "Erro ao atualizar o registro.";
            }
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }

    // BLOCO DE DELEÇÃO
    if ($acao == "deletar") {
        // Monta a query SQL para deletar registros filtrando pela coluna indicada
        $sql = "DELETE FROM $tabela WHERE $coluna_filtro = :valor_filtro";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":valor_filtro", $valor_filtro);

            if ($stmt->execute()) {
                echo "Registro deletado com sucesso!";
            } else {
                echo "Erro ao deletar o registro.";
            }
        } catch (PDOException $e) {
            echo "Erro na exclusão: " . $e->getMessage();
        }
    }
}

// Exibe dois botões de navegação após a ação (atualização ou exclusão)
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
