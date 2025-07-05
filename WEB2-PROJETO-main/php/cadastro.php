<?php
// C:\wamp64\www\WEB2-PROJETO-main\php\cadastro.php

// Inclui o arquivo de conexão com o banco de dados
// AVISO: Certifique-se que seu conexao.php retorna uma conexão MySQLi ($conn)
require_once 'conexao.php';

// Define o cabeçalho para retornar JSON
header('Content-Type: application/json');

// Verifica se a requisição é do tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe e sanitiza os dados do formulário com htmlspecialchars
    $cpf = htmlspecialchars(trim($_POST['cpf']), ENT_QUOTES, 'UTF-8');
    $senha = htmlspecialchars(trim($_POST['senha']), ENT_QUOTES, 'UTF-8'); // A senha será hashada (mas desativada temporariamente)
    $nome = htmlspecialchars(trim($_POST['nome']), ENT_QUOTES, 'UTF-8');
    $whatsapp = htmlspecialchars(trim($_POST['whatsapp']), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');

    // Validação básica dos dados
    if (empty($cpf) || empty($senha) || empty($nome) || empty($whatsapp) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos obrigatórios.']);
        exit();
    }

    // Validação de CPF (apenas números e 11 dígitos)
    if (!preg_match('/^[0-9]{11}$/', $cpf)) {
        echo json_encode(['status' => 'error', 'message' => 'CPF inválido. Use apenas 11 números.']);
        exit();
    }

    // Validação de WhatsApp (apenas números)
    if (!preg_match('/^[0-9]+$/', $whatsapp)) {
        echo json_encode(['status' => 'error', 'message' => 'WhatsApp inválido. Use apenas números.']);
        exit();
    }

    // Validação de e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Formato de e-mail inválido.']);
        exit();
    }

    // --- MODIFICAÇÃO TEMPORÁRIA: Senha sem criptografia ---
    // $senhaHash = password_hash($senha, PASSWORD_DEFAULT); // Criptografia desativada TEMPORARIAMENTE
    $senhaHash = $senha; // Senha em texto puro (APENAS PARA TESTES INICIAIS)
    // --- FIM MODIFICAÇÃO TEMPORÁRIA ---

    // Verifica se o CPF já existe
    $sql_check = "SELECT cpf FROM usuarios WHERE cpf = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $cpf);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'CPF já cadastrado. Tente outro ou faça login.']);
        $stmt_check->close();
        $conn->close();
        exit();
    }
    $stmt_check->close();

    // Prepara a instrução SQL para inserir dados
    $sql_insert = "INSERT INTO usuarios (cpf, senha, nome, whatsapp, email) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    // Associa os parâmetros e executa
    $stmt_insert->bind_param("sssss", $cpf, $senhaHash, $nome, $whatsapp, $email);

    if ($stmt_insert->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Cadastro realizado com sucesso! Você já pode fazer login.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar: ' . $stmt_insert->error]);
    }

    // Fecha a declaração e a conexão
    $stmt_insert->close();
    $conn->close();

} else {
    // Se não for uma requisição POST, não faça nada ou redirecione
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido.']);
}
?>