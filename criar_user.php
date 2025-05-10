<?php

define("USER", "root");
define("PASS", "root");

try {
    $pdo = new PDO("mysql:host=localhost", USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criação do banco
    $pdo->exec("CREATE DATABASE IF NOT EXISTS UsuariosSistema");
    $pdo->exec("USE UsuariosSistema");

    // Criação da tabela
    $criarTabela = "
        CREATE TABLE IF NOT EXISTS USERS (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            USUARIO VARCHAR(10) NOT NULL UNIQUE,
            SENHA VARCHAR(10),
            TIPO ENUM('admin', 'usuario') NOT NULL
        )";
    $pdo->exec($criarTabela);

} catch (PDOException $e) {
    die("Erro ao conectar/criar banco: " . $e->getMessage());
}

// Recebe dados do formulário
$usuario = $_POST['criarUser'] ?? '';
$senha = $_POST['InfoSenha'] ?? '';
$tipo = $_POST['tipoUsuario'] ?? '';

// Verifica se já existe o usuário
$stmt = $pdo->prepare("SELECT * FROM USERS WHERE USUARIO = :usuario");
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo  '<div style="text-align: center; margin-top: 20px;">
    <h3 style="color: red;">❌ Erro ao cadastrar usuário</h3> 
    <strong>Usuário já existe!</strong><br><br> 
    </div>';

    echo '<div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
    <a href="PageOne.html" style="text-decoration: none;">
        <button style="
            background: #1976d2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        " 
        onmouseover="this.style.background=\'#1565c0\'" 
        onmouseout="this.style.background=\'#1976d2\'">
            Voltar à Página Inicial
        </button>
    </a>

    <a href="criar_user.html" style="text-decoration: none;">
        <button style="
            background: #1976d2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        " 
        onmouseover="this.style.background=\'#1565c0\'" 
        onmouseout="this.style.background=\'#1976d2\'">
            Criar Usuario
        </button>
    </a>
    </div>';
    
    exit;
}

// Insere usuário
$insert = $pdo->prepare("INSERT INTO USERS (USUARIO, SENHA, TIPO) VALUES (:usuario, :senha, :tipo)");
$insert->bindParam(':usuario', $usuario);
$insert->bindParam(':senha', $senha);
$insert->bindParam(':tipo', $tipo);

if ($insert->execute()) {
    echo '<h3 style="text-align: center; margin-top: 20px;
    font-family: Arial, sans-serif;
    "> Usuário cadastrado com sucesso!</h3>';
} else {
    echo '<h3 style="text-align: center; margin-top: 20px;"> Erro ao cadastrar usuário.</h3>';
}

// Botões
echo '<div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
    <a href="PageOne.html" style="text-decoration: none;">
            <button style="
            background: #1976d2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        " 
        onmouseover="this.style.background=\'#1565c0\'" 
        onmouseout="this.style.background=\'#1976d2\'">
            Voltar à Página Inicial</button>
    </a>

    <a href="CadastroFornecedor.html" style="text-decoration: none;">
        <button style="text-decoration: none;">
            <button style="
            background: #1976d2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        " 
        onmouseover="this.style.background=\'#1565c0\'" 
        onmouseout="this.style.background=\'#1976d2\'">
        Cadastrar Novo Fornecedor</button>
    </a>
</div>';



?>
