<?php 

$endpoint = "https://agendavivasp.com.br/cultural-events?lat=-23.9653897&lng=-46.3306978";

$cURL = curl_init();

curl_setopt($cURL, CURLOPT_URL, $endpoint);

curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

if (curl_errno($cURL)) {
    echo "Erro no cURL: " . curl_error($cURL);
} else {
    $response = curl_exec($cURL);
    print($response);
}
curl_close($cURL);



// & "C:\xampp\php\php.exe" C:\xampp\htdocs\Projeto_Curl\AgendaSaoPaulo\api.php

//  Não consegui porque o HTML não me retoronou com estrutura boa
?>