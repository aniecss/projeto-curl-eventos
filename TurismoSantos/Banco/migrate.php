<?php 

require __DIR__ . "/database.php";

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

echo "Tabela criada com sucesso!";

// & "C:\xampp\php\php.exe" C:\xampp\htdocs\Projeto_Curl\TurismoSantos\Banco\migrate.php

?>