<?php

$host = "localhost";
$banco = "krikos_db";
$usuario = "root";
$senha = "12345678";

try {

    $conexao = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    $conexao->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conexao->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $erro) {

    error_log($erro->getMessage());

    http_response_code(500);

    exit("Erro ao conectar com o banco de dados.");

}
