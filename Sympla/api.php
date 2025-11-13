<?php

use Dom\XPath;

$endpoint = 'https://www.sympla.com.br/eventos?s=santos';

$cRUL = curl_init(); 

curl_setopt($cRUL, CURLOPT_URL, $endpoint);

curl_setopt($cRUL, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($cRUL); 

if (curl_errno($cRUL)) {
    echo "Erro cRUL: " . curl_error($cRUL); 
} else {
    print_r($response);
}

curl_close($cRUL);

// analisar o HTML

$istaEventos = [];

if ($response) {
    $html = new DOMDocument();
    @$html->loadHTML($response);

    $xpath = new DOMXPath($html);
    
    // Seleciona os containers 
    $xpath_container = "//div[contains(@class, 'k5k13s8"; 
    $eventos = $xpath->query($xpath_container);

    if ($eventos->length > 0) {
        echo "Eventos encontrados: " . $eventos->length . "\n";

        foreach($eventos as $sympla) {
            // Localiza a H3 e extrai o titulo do H3 dentro do container do evento
            $titulo_container = $xpath->query(".//h3", $sympla); 
            $titulo = $titulo_container->length > 0 ? trim($titulo_container->item(0)->textContent) : 'N/A (titulo não encontrado)';

             // Extrai o LINK
             $link_container = $xpath->query(".//a[contains(@href, '/eventos/')]/@href", $sympla);
             $link = $link_container->length > 0 ? $link_container->item(0)->getAttribute('href') : 'N/A (link não encontrado)';
        
             // Extrai a DATA dentro da 'card-data'
             $data_container = $xpath->query(".//time/@datetime | .//p[contains(text(), 'de')]/text()", $sympla);
             $data = $data_container->length > 0 ? trim($data_container->item(0)->textContent) : 'N/A (data não encontrada)';

             // Montar o array de eventos
             $listaEventos[] = [
                'titulo' => $titulo,
                'link' => $link,
                'data' => $data,
                'origem' => $endpoint
             ];
        } echo "Lista de eventos preenchida com sucesso!\n";
    } else {
        echo "Não foi possível encontrar cards de evento com o XPath '$xpath_container'.\n";
    }
}
    
//  NÃO CONSEGUI FINALIZAR O PROJETO PORQUE O HTML NÃO ME RETORNOU COM UMA ESTRUTRA QUE EU PUDESSE VISUALIZAR MELHOR
?
