<?php 

$endpoint = "https://santosconventioncenter.com.br/agenda/";

$cURL = curl_init();

curl_setopt($cURL, CURLOPT_URL, $endpoint);

curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

if (curl_errno($cURL)) {
    echo "Erro no ucURL: " . curl_error($cURL);
} else {
    $response = curl_exec($cURL);
    print($response);
}
curl_close($cURL);
// analisar HTML

$listaEventos = [];

if ($response) {
    $html = new DOMDocument();
    @$html->loadHTML($response);
    
    $xpath = new DOMXPath($html);

    $xpath_container = "//div[contains(@class, 'card-evento')]";
    $eventos = $xpath->query($xpath_container);

    if ($eventos->length > 0) {
        echo "Eventos encontrados: " . $eventos->length . "\n";

        foreach ($eventos as $agenda) {
            
            // Localizar o titulo e extrair
            $titulo_container = $xpath->query(".//p[contains(@class, 'titulo-evento')]", $agenda);
            $titulo = $titulo_container->length > 0 ? trim(($titulo_container->item(0)->textContent)) : 'N/A (titulo não encontrado)';

            // extrair a data
            $data_container = $xpath->query("//p[contains(@class, 'data-evento')]", $agenda);
            $data = $data_container->length > 0 ? trim($data_container->item(0)->textContent) : 'N/A (data não encontrada)';

            // extrair a descrição~
            $descricao_container = $xpath->query(".//p[contains(@class, 'descricao-evento')]", $agenda);
            $descricao = $descricao_container->length > 0 ? trim(($descricao_container->item(0)->textContent)) : 'N/A (descrição não encontrada)';
            
            // adicionar o evento à lista
            $listaEventos[] = [
                'titulo' => $titulo,
                'data' => $data,
                'descricao' => $descricao,
                'origem' => $endpoint
            ];
        }
        echo "Lista de Eventos adicionado com sucesso.\n";
    } else {
        echo "Nenhum evento encontrado.\n";
    }
}

// visualizar a lista de eventos
header('Content-Type: application/json; charset=utf-8');
echo json_encode($listaEventos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// salva em arquivo JSON
$arquivo = __DIR__ . "/agenda_santos.json";
file_put_contents($arquivo, json_encode($listaEventos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo "Arquivo Json em: " . $arquivo . "\n";

?>
