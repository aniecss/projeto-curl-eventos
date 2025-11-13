<?php

$arquivo = __DIR__ . '/eventos_turismo_santos.json';

// converte o arquivo
$eventos = json_decode(file_get_contents($arquivo), true);

if (!$eventos) {
    die("Erro ao ler o arquivo JSON.");
}

try {
    // Conecta no banco (ajusta usuário/senha conforme teu XAMPP)
    $pdo = new PDO("mysql:host=localhost;dbname=turismo_santos;charset=utf8", "root", "");

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar a query
    $stmt = $pdo->prepare("
        INSERT INTO eventos (titulo, descricao, link, data, origem)
        VALUES (:titulo, :descricao, :link, :data, :origem)
    ");

    // Percorre o JSON e inserir cada dado em uma coluna do evento
    foreach ($eventos as $e) {
        $stmt->execute([
            ':titulo' => $e['titulo'],
            ':descricao' => $e['Descrição'],
            ':link' => $e['link'],
            ':data' => $e['data'],
            ':origem' => $e['origem']
        ]);
    }

    echo " Eventos importados com sucesso!";
} catch (PDOException $err) {
    echo "Erro: " . $err->getMessage();
}
?>