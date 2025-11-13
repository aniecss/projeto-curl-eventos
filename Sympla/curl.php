<?php

use Dom\XPath;

$endpoint = 'https://www.sympla.com.br/eventos?s=santos';

$cRUL = curl_init(); // Variável inicializada

curl_setopt($cRUL, CURLOPT_URL, $endpoint);

curl_setopt($cRUL, CURLOPT_RETURNTRANSFER, true);

// CORREÇÃO: Usar $cRUL (a variável correta) em vez de $scRUL
$response = curl_exec($cRUL); 

if (curl_errno($cRUL)) {
    echo "Erro cRUL: " . curl_error($cRUL); // Melhorando a mensagem de erro
} else {
    print_r($response);
}

curl_close($cRUL);

// & "C:\xampp\php\php.exe" C:\xampp\htdocs\Projeto_Curl\Sympla/curl.php