<?php

/*  3 Projeto
    Bilheteria de Santos: Extração de eventos; 
*/

$endpoint = 'https://www.bilheteriaexpress.com.br/agendas/santos.html#page=1';

// fazer a requisição cURL
$cURL = curl_init();

curl_setopt($cURL, CURLOPT_URL, $endpoint);

// retornar o resultado da requisição
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

// executar a requisição
$response = curl_exec($cURL);

// verificar o erro da execução
if (curl_errno($cURL)) {
    echo "Erro cURL: " . curl_error($cURL);
} else {
    print($response);
}

// fechar a execução cURL
curl_close($cURL);

/* -------------------------------------
        ANALISE DO HTML 
------------------------------------- */

$listaEventos = [];

if ($response) {
    $html = new DOMDocument();
    @$html->loadHTML($response);

    $xpath = new DOMXPath($html);
    
    // selecionar os containers que posseuem os eventos
    $xpath_container = "//div[contains(@class, 'product-shop')]";; 
    $eventos = $xpath->query($xpath_container);

    if ($eventos->length > 0) {
        echo "Eventos encontrados: ". $eventos->length ; "\n";
        
        foreach ($eventos as $bilheteria) {
            // localizar a H2 e extrair o titulo
            $titulo_container = $xpath->query(".//h2", $bilheteria);
            $titulo = $titulo_container->length > 0 ? trim($titulo_container->item(0)->textContent) : 'N/A (titulo não encontrado)';
            
            // extrair a data
            $data_container = $xpath->query(".//div[@class='price-review']//div[contains(@style, 'font-weight:bold') and contains(@style, '13px')]", $bilheteria);
            $data = $data_container->length > 0 ? trim($data_container->item(0)->textContent) : 'N/A (data não encontrada)';

            // extrair a descrição
            $descricao_container = $xpath->query(".//h2[@class='product-name']/following-sibling::div[1]", $bilheteria);
            $descricao = $descricao_container->length > 0 ? trim($descricao_container->item(0)->textContent) : 'N/A (local não encontrado)';

            // extrair o preço
            $preco_container = $xpath->query(".//span[contains(@style, '#06aa48')]", $bilheteria);
            $preco = $preco_container->length > 0 ? trim($preco_container->item(0)->textContent) : 'N/A (preço não encontrado)';
            
            // adicionar o evento à lista
            $listaEventos[] = [
                'titulo' => $titulo,
                'data' => $data,
                'descricao' => $descricao,
                'preco' => $preco,
                'origem' => $endpoint
            ];
        }
        echo "Lista de eventos concluido com sucesso.\n";
    } else {
        echo "Nenhum evento encontrado. \n";
    }
}
// Exibir os resultados em formato JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($listaEventos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// Salver em arquivo JSON
$arquivo = __DIR__ . "/bilheteria_santos.json";
file_put_contents($arquivo,json_encode($listaEventos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Arquivo JSON salvo em: $arquivo\n";


?>
