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
