<?php

require __DIR__ . '/Banco/database.php';

$pdo = new PDO('mysql:host=127.0.0.1;dbname=eventos;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ------------------------------------------
// Turismo de Santos - Extração de eventos;
// ------------------------------------------

// Passo 1: Configuração e Requisição curl

// site do evento
$endpoint = 'https://www.turismosantos.com.br/pt-br/eventos';

// Iniciar cURL 
$cURL = curl_init();

// Definir a URL de destino
curl_setopt($cURL, CURLOPT_URL, $endpoint);

// Retornar o resultado da requisição como string
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

// Executar a requisição
$response = curl_exec($cURL);

// Verificar erros na execução
if (curl_errno($cURL)) {
    echo "Erro cURL: " . curl_error($cURL);
} else {
    // print($response);
}

// Fechar a conexão cURL
curl_close($cURL);

// --------------------------------------
// Passo 2: Anlisando a HTML
// --------------------------------------

$listaEventos = [];

if ($response) {
    $html = new DOMDocument();

    // @ informar HTML malformado 
    @$html->loadHTML($response);
    $xpath = new DOMXPath($html);

    // Seleciona os containers que possuem eventos
    $xpath_container = "//div[contains(@class, 'col-md-4')]";
    $eventos = $xpath->query($xpath_container);

    if ($eventos->length > 0) {
        echo "Eventos encontrados: " . $eventos->length . "\n";
        
        // preparar para inserir no banco de dados
        $inserir = $pdo->prepare("
            INSERT INTO eventos (titulo, descricao, link, data, origem)
            VALUES (:titulo, :descricao, :link, :data, :origem)
        ");

        foreach ($eventos as $santos) {
            
            // Localiza a H3 e extrai o titulo do H3
            $titulo_s = $xpath->query(".//h3", $santos);
            $titulo = $titulo_s->length > 0 ? trim($titulo_s->item(0)->textContent) : null;
         
            // Extrair a Descrição
            $descricao_container = $xpath->query(".//p", $santos);
            $descricao = $descricao_container->length > 0 ? trim($descricao_container->item(0)->textContent) : null;

            // Extrai o LINK
            $link_s = $xpath->query(".//a", $santos);
            $link = $link_s->length > 0 ? $link_s->item(0)->getAttribute('href') : null;

            // Extrai a DATA dentro da 'card-data'
            $data_s = $xpath->query(".//div[@class='card-data']", $santos);
            $data = $data_s->length > 0 ? trim($data_s->item(0)->textContent) : null;

            if ($titulo|| $data) {
                continue;
            }

            // verificar se a duplicação antes de inserir no banco de dados
            $verificar = $pdo->prepare("
                SELECT id FROM eventos
                WHERE titulo = :titulo AND data = :data
            ");

            $verificar->execute([
                ':titulo' => $titulo,
                ':data' => $data
            ]);

            // fetch retornar falso, vai inserir no banco de dados
            $existe = $verificar->fetch();

            if (!$existe) {
                // não existe, ele vai inserir
                $inserir->execute([
                    ':titulo' => $titulo,
                    ':descricao' => $descricao,
                    ':link' => $link,
                    ':data' => $data,
                    ':origem' => $endpoint
                ]);
            }

            // Montar o array de eventos para adicionar no JSON
            $listaEventos[] = [
                'titulo' => $titulo,
                'Descrição' => $descricao,
                'link' => $link,
                'data' => $data,
                'origem' => $endpoint
            ];
        }
        
        echo "Lista de eventos preenchida com sucesso!\n";
        
    } else {
        echo "Não foi possível encontrar cards de evento com o XPath '$xpath_container'.\n";
    }
}

// -------------------------
// Exibir em JSON
// -------------------------

header('Content-Type: application/json; charset=utf-8');
echo json_encode($listaEventos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);


// & "C:\xampp\php\php.exe" C:\xampp\htdocs\Projeto_Curl\Curls.php

// & "C:\xampp\php\php.exe" C:\xampp\htdocs\Projeto_Curl\TurismoSantos\api.php