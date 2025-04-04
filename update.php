<?php
define("USER", "root");
define("PASS", "root");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=DadosFornecedor", USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tabela = $_POST['tabela'];
    $coluna_filtro = $_POST['coluna_filtro'];
    $valor_filtro = $_POST['valor_filtro'];
    $acao = $_POST['acao'];

    // Valida os nomes de tabela e colunas para evitar SQL Injection
    $valida_nome = function($str) {
        return preg_match('/^[a-zA-Z0-9_]+$/', $str);
    };

    if (!$valida_nome($tabela) || !$valida_nome($coluna_filtro)) {
        die("Erro: Nome de tabela ou coluna inválido.");
    }

    // Atualização
    if ($acao == "atualizar") {
        $coluna_alterar = $_POST['coluna_alterar'];
        $novo_valor = $_POST['novo_valor'];

        if (!$coluna_alterar || !$valida_nome($coluna_alterar)) {
            die("Erro: Coluna para alterar inválida ou não preenchida.");
        }

        $sql = "UPDATE $tabela SET $coluna_alterar = :novo_valor WHERE $coluna_filtro = :valor_filtro";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":novo_valor", $novo_valor);
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

    // Exclusão
    if ($acao == "deletar") {
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
