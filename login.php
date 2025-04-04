<?php
session_start(); // Inicia a sessão para armazenar dados do usuário logado

// Conexão com o banco (ajuste com seus dados)
$host = 'localhost'; // Nome do host do banco de dados
$db = 'sistema';     // Nome do banco de dados
$user = 'root';      // Nome de usuário do banco
$pass = 'root';      // Senha do banco

try {
    // Cria a conexão com o banco usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Define o modo de erro para exceção
} catch (PDOException $e) {
    // Em caso de erro na conexão, exibe a mensagem e encerra
    die("Erro na conexão: " . $e->getMessage());
}

// Captura os dados do formulário enviados via POST
$tipo_usuario = $_POST['tipo_usuario']; // Tipo de usuário (admin ou usuário)
$usuario = $_POST['usuario'];           // Nome de usuário
$senha = $_POST['senha'];               // Senha

// Consulta no banco de dados para verificar se existe o usuário com essas credenciais
$sql = "SELECT * FROM usuarios WHERE usuario = :usuario AND senha = :senha AND tipo = :tipo";
$stmt = $pdo->prepare($sql); // Prepara a query para evitar SQL Injection
$stmt->bindParam(':usuario', $usuario); // Substitui :usuario pelo valor digitado
$stmt->bindParam(':senha', $senha);     // Substitui :senha pelo valor digitado
$stmt->bindParam(':tipo', $tipo_usuario); // Substitui :tipo pelo valor selecionado
$stmt->execute(); // Executa a query

if ($stmt->rowCount() > 0) { // Se encontrou algum usuário com essas credenciais
    $dados = $stmt->fetch(PDO::FETCH_ASSOC); // Pega os dados do usuário encontrado
    $_SESSION['usuario'] = $dados['usuario']; // Salva o nome do usuário na sessão
    $_SESSION['tipo'] = $dados['tipo'];       // Salva o tipo de usuário na sessão

    if ($dados['tipo'] == 'admin') {
        header("Location: PageOne.html"); // Redireciona para a página do administrador
    } else {
        header("Location: usuario.php"); // Redireciona para a página do usuário comum
    }
    exit(); // Encerra o script após o redirecionamento
} else {
    // Se as credenciais estiverem incorretas, exibe um alerta e volta à página anterior
    echo "<script>alert('Usuário ou senha inválidos!'); window.history.back();</script>";
}
?>
