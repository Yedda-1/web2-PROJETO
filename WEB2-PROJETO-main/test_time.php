<?php
// C:\wamp64\www\WEB2-PROJETO-main\test_time.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Explicitamente define o fuso horário para garantir que a leitura seja consistente
// Use o mesmo fuso horário que você configurou no seu php.ini
date_default_timezone_set('America/Sao_Paulo'); 

echo "<h1>Verificação de Fuso Horário e Hora do PHP</h1>";
echo "PHP date.timezone do ini: " . ini_get('date.timezone') . "<br>";
echo "Fuso horário padrão para date(): " . date_default_timezone_get() . "<br>";
echo "Data e Hora Atual do PHP: " . date('Y-m-d H:i:s') . "<br>";
echo "Data e Hora Atual do PHP (com timezone): " . date('Y-m-d H:i:s T') . "<br>"; // 'T' mostra o acrônimo do timezone (e.g., BRT)
echo "Data e Hora Atual do PHP (formato UTC): " . gmdate('Y-m-d H:i:s') . " UTC<br>";
echo "Hora de Expiração (+1 hora a partir de agora): " . date('Y-m-d H:i:s', strtotime('+1 hour')) . "<br>";
?>