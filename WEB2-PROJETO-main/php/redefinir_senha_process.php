<?php
// C:\wamp64\www\WEB2-PROJETO-main\php\redefinir_senha_process.php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/wamp64/logs/apache_error.log'); // Verifique este caminho no seu WampServer

error_log("DEBUG: redefinir_senha_process.php - Inicio do script.");

session_start();
header('Content-Type: application/json');

require_once 'conexao.php'; // Inclui a conexão com o banco de dados

$response = ['status' => 'error', 'message' => 'Erro desconhecido.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("DEBUG: redefinir_senha_process.php - Requisição POST recebida.");

    $token = $_POST['token'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirma_senha = $_POST['confirma_senha'] ?? '';

    error_log("DEBUG: redefinir_senha_process.php - Token recebido: " . $token);

    if (empty($token) || empty($nova_senha) || empty($confirma_senha)) {
        $response['message'] = 'Todos os campos são obrigatórios.';
        error_log("DEBUG ERRO: redefinir_senha_process.php - Campos obrigatórios ausentes.");
        echo json_encode($response);
        exit();
    }

    if ($nova_senha !== $confirma_senha) {
        $response['message'] = 'As senhas não coincidem.';
        error_log("DEBUG ERRO: redefinir_senha_process.php - Senhas não coincidem.");
        echo json_encode($response);
        exit();
    }

    if (strlen($nova_senha) < 6) { // Exemplo de validação de senha
        $response['message'] = 'A senha deve ter pelo menos 6 caracteres.';
        error_log("DEBUG ERRO: redefinir_senha_process.php - Senha muito curta.");
        echo json_encode($response);
        exit();
    }

    try {
        // 1. Validar o token e a expiração
        // Procura um usuário com o token fornecido e que o token ainda não tenha expirado.
        $stmt = $conn->prepare("SELECT email FROM usuarios WHERE reset_token = :token AND token_expiracao > NOW() - INTERVAL 1 MINUTE");
        $stmt->execute([':token' => $token]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $response['message'] = 'Token inválido ou expirado. Por favor, solicite uma nova redefinição.';
            error_log("DEBUG ERRO: redefinir_senha_process.php - Token inválido ou expirado.");
            echo json_encode($response);
            exit();
        }

        $user_email = $usuario['email']; // O email do usuário cujo token foi validado.

        // 2. Hash da nova senha
        $senha_hashed = password_hash($nova_senha, PASSWORD_DEFAULT);

        // 3. Atualizar a senha e limpar o token no banco de dados
        $stmt_update = $conn->prepare("UPDATE usuarios SET senha = :senha, reset_token = NULL, token_expiracao = NULL WHERE email = :email");
        $stmt_update->execute([
            ':senha' => $senha_hashed,
            ':email' => $user_email
        ]);

        $response['status'] = 'success';
        $response['message'] = 'Senha redefinida com sucesso! Você pode fazer login agora.';
        error_log("DEBUG: redefinir_senha_process.php - Senha redefinida com sucesso para: " . $user_email);

    } catch (PDOException $e) {
        $response['message'] = 'Erro ao redefinir a senha: ' . $e->getMessage();
        error_log("DEBUG ERRO: redefinir_senha_process.php - Erro PDO: " . $e->getMessage());
    } catch (Exception $e) {
        $response['message'] = 'Ocorreu um erro inesperado: ' . $e->getMessage();
        error_log("DEBUG ERRO: redefinir_senha_process.php - Erro genérico: " . $e->getMessage());
    }
} else {
    $response['message'] = 'Método de requisição inválido.';
    error_log("DEBUG ERRO: redefinir_senha_process.php - Método de requisição inválido.");
}

echo json_encode($response);
exit();
?>