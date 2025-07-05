<?php
// C:\wamp64\www\WEB2-PROJETO-main\php\solicitar_redefinicao.php

// Configurações para exibir e logar erros detalhados (essencial para depuração)
error_reporting(E_ALL);
ini_set('display_errors', 1); // Exibe erros no navegador
ini_set('log_errors', 1);     // Garante que erros também sejam logados
ini_set('error_log', 'C:/wamp64/logs/apache_error.log'); // Verifique este caminho no seu WampServer

// DEBUG: Ponto 1 - Início do script
error_log("DEBUG: solicitacao_redefinicao.php - Ponto 1: Inicio do script.");

session_start();
header('Content-Type: application/json'); // Garante que a resposta será tratada como JSON

// Inclui o arquivo de conexão com o banco de dados
require_once 'conexao.php';

// Inicializa a variável de resposta
$response = ['status' => 'error', 'message' => 'Erro desconhecido.'];

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DEBUG: Ponto 2 - Requisição POST recebida
    error_log("DEBUG: solicitacao_redefinicao.php - Ponto 2: Requisição POST recebida.");

    // Verifica se o campo 'identificador' foi enviado
    if (isset($_POST['identificador'])) {
        $identificador = $_POST['identificador'];

        // DEBUG: Ponto 2.5 - Identificador recebido
        error_log("DEBUG: solicitacao_redefinicao.php - Ponto 2.5: Identificador recebido: " . $identificador);

        try {
            // Prepara a consulta SQL para encontrar o usuário por CPF ou E-mail
            // Ajustado para selecionar 'email' e 'cpf' e usar 'cpf' como PK para a busca.
            $sql = "SELECT email, cpf FROM usuarios WHERE email = :identificador OR cpf = :identificador";
            $stmt = $conn->prepare($sql);

            // DEBUG: Ponto 3 - Consulta preparada
            error_log("DEBUG: solicitacao_redefinicao.php - Ponto 3: Consulta SQL preparada.");

            // Vincula os valores aos placeholders e executa a consulta
            $stmt->execute([':identificador' => $identificador]);

            // DEBUG: Ponto 4 - Consulta executada
            error_log("DEBUG: solicitacao_redefinicao.php - Ponto 4: Consulta SQL executada.");

            // Obtém o resultado da consulta
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // DEBUG: Ponto 5 - Resultado da consulta
            error_log("DEBUG: solicitacao_redefinicao.php - Ponto 5: Resultado fetch: " . ($usuario ? "Encontrado" : "Não encontrado"));

            if ($usuario) {
                $user_email = $usuario['email'];
                $user_cpf = $usuario['cpf'];

                // 1. Gerar um token único e seguro
                $token = bin2hex(random_bytes(32));

                // 2. Definir a validade do token (ex: 1 hora a partir de agora)
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                try {
                    // 3. Salvar (ou atualizar) o token e a data de expiração na tabela 'usuarios'
                    $stmt_update_token = $conn->prepare("UPDATE usuarios SET reset_token = :token, token_expiracao = :expires WHERE email = :email OR cpf = :cpf");
                    $stmt_update_token->execute([
                        ':token' => $token,
                        ':expires' => $expires,
                        ':email' => $user_email,
                        ':cpf' => $user_cpf
                    ]);
                    error_log("DEBUG: solicitacao_redefinicao.php - Token salvo/atualizado para usuário: " . $user_email);

                    // 4. Preparar o link de redefinição que será enviado por e-mail
                    $reset_link = "http://localhost/WEB2-PROJETO-main/redefinir_senha.html?token=" . $token;

                    // AQUI VIRIA A FUNÇÃO REAL DE ENVIO DE E-MAIL (Ex: PHPMailer)
                    error_log("DEBUG: solicitacao_redefinicao.php - Link de redefinição gerado: " . $reset_link);
                    // Exemplo: sendEmail($user_email, "Redefinição de Senha", "Clique aqui para redefinir sua senha: " . $reset_link);

                    $response['status'] = 'success';
                    $response['message'] = 'Instruções de redefinição de senha enviadas para o e-mail associado.';
                    error_log("DEBUG: solicitacao_redefinicao.php - Ponto 6: Usuário encontrado, processo de redefinição iniciado.");

                } catch (PDOException $e) {
                    $response['status'] = 'error';
                    $response['message'] = 'Erro interno ao gerar ou salvar o token de redefinição.';
                    error_log("DEBUG ERRO: solicitacao_redefinicao.php - Erro ao salvar token: " . $e->getMessage());
                }
            } else {
                // Usuário não encontrado
                $response['status'] = 'error';
                $response['message'] = 'Nenhum usuário encontrado com o CPF ou E-mail fornecido.';
                error_log("DEBUG: solicitacao_redefinicao.php - Ponto 7: Usuário não encontrado.");
            }

        } catch (PDOException $e) {
            $response['message'] = 'Erro ao consultar o banco de dados: ' . $e->getMessage();
            error_log("DEBUG ERRO: solicitacao_redefinicao.php - Ponto 8: Erro PDO: " . $e->getMessage());
        } catch (Exception $e) {
            $response['message'] = 'Ocorreu um erro inesperado: ' . $e->getMessage();
            error_log("DEBUG ERRO: solicitacao_redefinicao.php - Ponto 9: Erro genérico: " . $e->getMessage());
        }
    } else {
        $response['message'] = 'O campo identificador é obrigatório.';
        error_log("DEBUG ERRO: solicitacao_redefinicao.php - Ponto 10: Campo identificador ausente.");
    }
} else {
    $response['message'] = 'Método de requisição inválido.';
    error_log("DEBUG ERRO: solicitacao_redefinicao.php - Ponto 11: Método de requisição inválido.");
}

error_log("DEBUG: solicitacao_redefinicao.php - Ponto 12: Enviando resposta JSON: " . json_encode($response));
echo json_encode($response);
exit();
?>