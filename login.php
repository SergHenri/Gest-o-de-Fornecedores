<?php
session_start();

// Conexão com o banco (ajuste com seus dados)
$host = 'localhost';
$db = 'sistema';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Captura os dados do formulário
$tipo_usuario = $_POST['tipo_usuario'];
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

// Consulta no banco de dados
$sql = "SELECT * FROM usuarios WHERE usuario = :usuario AND senha = :senha AND tipo = :tipo";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':usuario', $usuario);
$stmt->bindParam(':senha', $senha);
$stmt->bindParam(':tipo', $tipo_usuario);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['usuario'] = $dados['usuario'];
    $_SESSION['tipo'] = $dados['tipo'];

    if ($dados['tipo'] == 'admin') {
        header("Location: PageOne.html");
    } else {
        header("Location: usuario.php");
    }
    exit();
} else {
    echo "<script>alert('Usuário ou senha inválidos!'); window.history.back();</script>";
}
?>
