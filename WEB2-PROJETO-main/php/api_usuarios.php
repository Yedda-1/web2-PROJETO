<?php
// Endereço de teste do Web Service:
// http://localhost/WEB2-PROJETO-main/php/api_usuarios.php

include 'conexao.php';

// Verifique se $conn é um objeto mysqli válido
if ($conn instanceof mysqli) {
    $query = "SELECT cpf, nome, whatsapp, email, data_cadastro FROM usuarios";
    $result = $conn->query($query);

    if ($result) {
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($usuarios);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Erro ao consultar o banco de dados']);
    }
    $conn->close();
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erro na conexão com o banco de dados']);
}
?>
