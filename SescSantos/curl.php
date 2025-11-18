<?php 

/*  2 Projeto
    Sesc Santos: Extração de eventos; 
*/
 // ------------------
 // PASSO 1: CONFIGURAÇÃO E REQUISIÇÃO cURL
 // ------------------

 $endpoint = 'https://www.sescsp.org.br/unidades/santos/';

 // fazer a requisição cURL
 $cRuL = curl_init();
 
 // definir a URL
 curl_setopt($cRuL, CURLOPT_URL, $endpoint);

 // retornar o resultado da requisição como string em vez de imprimir diretamente
 curl_setopt($cRuL, CURLOPT_RETURNTRANSFER, true);

 // executar a requisição
 
$response = curl_exec($cRuL);

// verificar o erro da execução

if (curl_errno($cRuL)) {
    echo "Erro cURL: " . curl_error($cRuL);
} else{
    print($response);
}
// fechar a execução cURL
curl_close($cRuL);
