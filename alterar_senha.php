<?php
session_start();

// Definindo as constantes de conexão
define("USER", "root");
define("PASS", "root");

try {
    // Conecta ao banco de dados UsuariosSistema
    $pdoUsuarioSistema = new PDO("mysql:host=localhost;dbname=UsuariosSistema", USER, PASS);
    $pdoUsuarioSistema->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}

// Verifica se os parâmetros de usuário estão na URL
if (!isset($_GET['usuario'])) {
    echo 'Dados inválidos.';
    exit();
}

$usuario = $_GET['usuario'];

// Verifica se o usuário existe no banco de dados
$stmt = $pdoUsuarioSistema->prepare("SELECT * FROM users WHERE usuario = :usuario");
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    echo 'Usuário inválido.';
    exit();
}

// Exibe o formulário para o usuário alterar a senha
?>

<form action="processar_senha.php" method="POST">
    <input type="hidden" name="usuario" value="<?= htmlspecialchars($usuario) ?>">
    
    <label for="nova_senha">Nova Senha:</label>
    <input type="password" name="nova_senha" id="nova_senha" required>
    
    <button type="submit">Alterar Senha</button>
</form>
