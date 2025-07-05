<?php
// Inicia a sessão (essencial para acessar e destruir variáveis de sessão)
session_start();

// Destrói todas as variáveis de sessão
$_SESSION = array();

// Se a sessão for usada para manter o cookie, também o destrói
// Nota: Isso irá destruir a sessão, e não apenas os dados da sessão!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrói a sessão
session_destroy();

// Redireciona o usuário de volta para a página de cadastro
header("Location: ../cadastro.html"); // Voltar uma pasta para encontrar cadastro.html
exit; // Garante que o script pare de executar após o redirecionamento
?>