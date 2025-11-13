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
 
 // definir a URL de destino
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

/* -------------------------------------
        ANALISE DO HTML
------------------------------------- */

$listaEventos = [];

if ($response) {
    $html = new DOMDocument();
    
    // informar HTML malformado 
    @$html->loadHTML($response);

    $xpath = new DOMXPath($html);

    // selecionar os containers que posseuem os eventos
    $xpath_container = ("//div[contains(@class, 'carrossel-home-item')]");  // //div[contains(@class, 'carrossel-home-item-thumbnail')]
    $eventos = $xpath->query($xpath_container);

    if ($eventos->length > 0) {
        echo "Eventos encontrados: " . $eventos->length . "\n";

        foreach ($eventos as $sesc) {
            
            // localizar a H3 e extrair o titulo do H3 dentro do container do evento
            $titulo_container = $xpath->query(".//h3", $sesc); 
            $titulo = $titulo_container->length > 0 ? trim($titulo_container->item(0)->textContent) : 'N/A (titulo não encontrado)';

            // extrair a Descrição
            $descricao_container = $xpath->query(".//p", $sesc);
            $descricao = $descricao_container->length > 0 ? trim($descricao_container->item(0)->textContent) : 'N/A (descrição não encontrada)';

            // extrair o LINK
            $link_container = $xpath->query(".//a", $sesc);
            $link = $link_container->length > 0 ? $link_container->item(0)->getAttribute('href') : 'N/A (link não encontrado)';


            // montar o array de eventos
            $listaEventos[] = [
                'titulo' => $titulo,
                'Descrição' => $descricao,
                'link' => $link,
                'origem' => $endpoint
            ];
        }
        echo "Lista de eventos preenchida com sucesso!\n";
        // print_r($listaEventos);
    }else {
        echo "Não foi possível encontrar cards de evento com o XPath '$xpath_container'.\n";
    }
}
// Exibir os resultados em formato JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($listaEventos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// Salver em arquivo JSON
$arquivo = __DIR__ . 'sesc_santos_eventos.json';
file_put_contents($arquivo,json_encode($listaEventos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\nArquivo JSON salvo $arquivo\n";

// & "C:\xampp\php\php.exe" C:\xampp\htdocs\Projeto_Curl\SescSantos\api.php
?>