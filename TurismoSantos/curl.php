<?php


// ------------------------------------------
// PASSO 1: CONFIGURAÇÃO E REQUISIÇÃO cURL
// ------------------------------------------

// URL do site de eventos
$endpoint = 'https://www.turismosantos.com.br/pt-br/eventos';

// Iniciar cURL — cria uma instância para manipular a requisição
$cURL = curl_init();

// Definir a URL de destino
curl_setopt($cURL, CURLOPT_URL, $endpoint);

// Retornar o resultado da requisição como string em vez de imprimir diretamente
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

// Executar a requisição
$response = curl_exec($cURL);

// Verificar erros na execução
if (curl_errno($cURL)) {
    echo "Erro cURL: " . curl_error($cURL);
} else {
    print($response);
}

// Fechar a conexão cURL
curl_close($cURL);

?>
