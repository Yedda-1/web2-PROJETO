<?php
header('Content-Type: application/json'); // Define o cabeçalho para indicar que a resposta é JSON

// Inclui o arquivo de conexão com o banco de dados
require_once 'conexao.php'; // Caminho correto, pois estão na mesma pasta

$response = array('status' => 'error', 'message' => 'Erro desconhecido.');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coleta e sanitiza os dados do formulário com htmlspecialchars
    $cpf = htmlspecialchars(trim($_POST['cpf']), ENT_QUOTES, 'UTF-8');
    $senha = htmlspecialchars(trim($_POST['senha']), ENT_QUOTES, 'UTF-8');

    if (empty($cpf) || empty($senha)) {
        $response['message'] = 'Por favor, preencha todos os campos.';
    } else {
        // Prepara a consulta SQL para buscar o usuário pelo CPF
        $stmt = $conn->prepare("SELECT cpf, senha FROM usuarios WHERE cpf = ?");
        
        if ($stmt) {
            $stmt->bind_param("s", $cpf);
            $stmt->execute();
            $stmt->store_result();
            
            // Se encontrou um usuário com o CPF
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($db_cpf, $db_senha_armazenada); // Mudado de $hashed_password para $db_senha_armazenada
                $stmt->fetch();
                
                // --- MODIFICAÇÃO TEMPORÁRIA: Verificação de senha sem criptografia ---
                // if (password_verify($senha, $db_senha_armazenada)) { // Verificação de senha criptografada desativada TEMPORARIAMENTE
                if ($senha === $db_senha_armazenada) { // Comparação de senha em texto puro (APENAS PARA TESTES INICIAIS)
                    $response['status'] = 'success';
                    $response['message'] = 'Login realizado com sucesso! Bem-vindo(a).';
                    // Iniciar sessão e armazenar o CPF
                     session_start();
                    $_SESSION['cpf'] = $db_cpf;
                    $_SESSION['loggedin'] = true;
                } else {
                    $response['message'] = 'Senha incorreta.';
                }
            } else {
                $response['message'] = 'CPF não cadastrado.';
            }
            $stmt->close();
        } else {
            $response['message'] = 'Erro ao preparar a consulta: ' . $conn->error;
        }
    }
} else {
    $response['message'] = 'Método de requisição inválido.';
}

// Fecha a conexão com o banco de dados
$conn->close();

echo json_encode($response); // Retorna a resposta em formato JSON