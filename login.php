<?php
session_start();

// Dados de conexão com o banco
define("HOST", "localhost");
define("DBNAME", "sistema"); // Ou "sistema", escolha o nome correto
define("USER", "root");
define("PASS", "root");

try {
    // Conecta ao banco usando PDO
    $pdo = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Captura os dados do formulário
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $tipo_usuario = $_POST['tipo_usuario'];

    // Consulta SQL com tipo de usuário incluso
    $sql = "SELECT * FROM usuarios WHERE usuario = :usuario AND senha = :senha AND tipo = :tipo";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':tipo', $tipo_usuario);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        // Salva informações do usuário na sessão
        $_SESSION['usuario'] = $dados['usuario'];
        $_SESSION['tipo'] = $dados['tipo'];

        // Redireciona com base no tipo de usuário
        if ($dados['tipo'] == 'admin') {
            header("Location: PageOne.html"); // Página do administrador
        } else {
            header("Location: visualizar.php"); // Página para usuário comum
        }
        exit();
    } else {
        echo "<script>alert('Usuário ou senha inválidos!'); window.history.back();</script>";
    }
}
?>
