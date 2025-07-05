<?php
// C:\wamp64\www\WEB2-PROJETO-main\php\conexao.php

// Configurações para exibir e logar erros detalhados (apenas para depuração, remova em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/wamp64/logs/apache_error.log'); // Verifique este caminho no seu WampServer

// Parâmetros de conexão com o banco de dados
$host = 'localhost';
$dbname = 'web2'; // Confirme que este é o nome correto do seu banco de dados
$user = 'root';
$password = ''; // Se você não tem senha para o root no Wamp, deixe em branco

// Tenta estabelecer a conexão usando PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    // Define o modo de erro para lançar exceções em caso de problemas
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("DEBUG: conexao.php - Conexao com o banco de dados estabelecida com sucesso.");
} catch (PDOException $e) {
    error_log("DEBUG ERRO: conexao.php - Erro de conexao: " . $e->getMessage());
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}
?>