<?php

/* Primeiro projeto
    Turismo de Santos - Extração de eventos;
*/

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
    // Exibe o HTML obtido (pode comentar essa linha depois pra não poluir o terminal)
    // print($response);
}

// Fechar a conexão cURL
curl_close($cURL);

// ----------------------------------------------------
// ANÁLISE DO HTML
// ----------------------------------------------------

$listaEventos = [];

if ($response) {
    $html = new DOMDocument();

    // @ suprime avisos de HTML malformado (muito comum em sites reais)
    @$html->loadHTML($response);

    $xpath = new DOMXPath($html);

    // Seleciona os containers que possuem eventos
    $xpath_container = "//div[contains(@class, 'col-md-4')]";
    $eventos = $xpath->query($xpath_container);

    if ($eventos->length > 0) {
        echo "Eventos encontrados: " . $eventos->length . "\n";

        foreach ($eventos as $santos) {
            
            // Localiza a H3 e extrai o titulo do H3 dentro do container do evento
            $titulo_s = $xpath->query(".//h3", $santos);
            $titulo = $titulo_s->length > 0 ? trim($titulo_s->item(0)->textContent) : 'N/A';
         
            // Extrair a Descrição
            $descricao_container = $xpath->query(".//p", $santos);
            $descricao = $descricao_container->length > 0 ? trim($descricao_container->item(0)->textContent) : 'N/A';

            // Extrai o LINK
            $link_s = $xpath->query(".//a", $santos);
            $link = $link_s->length > 0 ? $link_s->item(0)->getAttribute('href') : 'N/A';

            // Extrai a DATA dentro da 'card-data'
            $data_s = $xpath->query(".//div[@class='card-data']", $santos);
            $data = $data_s->length > 0 ? trim($data_s->item(0)->textContent) : 'N/A';

            // Montar o array de eventos
            $listaEventos[] = [
                'titulo' => $titulo,
                'Descrição' => $descricao,
                'link' => $link,
                'data' => $data,
                'origem' => $endpoint
            ];
        }
        
        echo "Lista de eventos preenchida com sucesso!\n";
        // print_r($listaEventos);   // imprimir listaEventos no terminal
        
    } else {
        echo "Não foi possível encontrar cards de evento com o XPath '$xpath_container'.\n";
    }
}

// -------------------------
// EXIBIR RESULTADOS 
// -------------------------

header('Content-Type: application/json; charset=utf-8');
echo json_encode($listaEventos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// -------------------------
// Salvar em arquivo JSON
// -------------------------

$arquivo = __DIR__ . '/eventos_turismo_santos.json';
file_put_contents($arquivo,json_encode($listaEventos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\nArquivo salvo\n";

// & "C:\xampp\php\php.exe" C:\xampp\htdocs\Projeto_Curl\Curls.php

// & "C:\xampp\php\php.exe" C:\xampp\htdocs\Projeto_Curl\TurismoSantos\api.php


$eventos = json_decode(file_get_contents('eventos_turismo_santos.json'), true);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (isset($eventos[$id])) {
    $evento = $eventos[$id];
    echo "<h2>{$evento['titulo']}</h2>";
    echo "<p><strong>Data:</strong> {$evento['data']}</p>";
    echo "<p>{$evento['Descrição']}</p>";
    echo "<a href='{$evento['link']}' target='_blank'>Ver mais</a><br><br>";

    // Botões para navegar
    if ($id > 0) {
        echo "<a href='?id=" . ($id - 1) . "'>⬅️ Anterior</a> ";
    }
    if ($id < count($eventos) - 1) {
        echo "<a href='?id=" . ($id + 1) . "'>Próximo ➡️</a>";
    }
} else {
    echo "Evento não encontrado.";
}