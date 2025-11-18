<?php 

$pdo = new PDO('mysql:host=127.0.0.1;dbname=eventos;charset=utf8', 'root', '');

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// criar a tabela de eventos

$pdo->exec("
    CREATE TABLE IF NOT EXISTS eventos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(500),
        descricao TEXT,
        link TEXT,
        data VARCHAR(50),
        origem TEXT
    );
");


?>