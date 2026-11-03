<?php
// Simulação do Banco de Dados de Churros
$mais_vendidos = [
    ["nome" => "Churros 1", "desc" => "Doce de Leite", "preco" => "R$ 8,00"],
    ["nome" => "Porção 4", "desc" => "Mini Churros c/ Nutella", "preco" => "R$ 22,00"],
    ["nome" => "Churros 7", "desc" => "Kinder Bueno", "preco" => "R$ 12,00"]
];

$churros_individuais = [
    ["nome" => "Churros 1", "desc" => "Doce de Leite Tradicional", "preco" => "R$ 8,00"],
    ["nome" => "Churros 2", "desc" => "Chocolate Ao Leite", "preco" => "R$ 8,00"],
    ["nome" => "Churros 3", "desc" => "Beijinho / Coco", "preco" => "R$ 9,00"]
];

$porcoes = [
    ["nome" => "Porção 1", "desc" => "10 Mini Churros D. Leite", "preco" => "R$ 20,00"],
    ["nome" => "Porção 2", "desc" => "10 Mini Churros Chocolate", "preco" => "R$ 20,00"],
    ["nome" => "Porção 3", "desc" => "15 Mini Churros Misto", "preco" => "R$ 28,00"]
];

// Identifica a página para destacar a navegação e montar o cardápio compartilhado.
$page = 'churros';
$title = 'Kriko’s — churros';
$individuais = $churros_individuais;
require __DIR__ . '/includes/cardapio.php';
