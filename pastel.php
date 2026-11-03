<?php
// Simulação do Banco de Dados de Pastéis
$mais_vendidos = [
    ["nome" => "Pastel 1", "desc" => "Carne c/ Queijo", "preco" => "R$ 12,00"],
    ["nome" => "Porção 4", "desc" => "Mini Pastéis Misto", "preco" => "R$ 28,00"],
    ["nome" => "Pastel 7", "desc" => "Especial da Casa", "preco" => "R$ 16,00"]
];

$pasteis_individuais = [
    ["nome" => "Pastel 1", "desc" => "Carne Moída", "preco" => "R$ 10,00"],
    ["nome" => "Pastel 2", "desc" => "Frango c/ Catupiry", "preco" => "R$ 11,00"],
    ["nome" => "Pastel 3", "desc" => "Queijo Mussarela", "preco" => "R$ 10,00"]
];

$porcoes = [
    ["nome" => "Porção 1", "desc" => "12 Mini Pastéis Carne", "preco" => "R$ 25,00"],
    ["nome" => "Porção 2", "desc" => "12 Mini Pastéis Queijo", "preco" => "R$ 25,00"],
    ["nome" => "Porção 3", "desc" => "20 Mini Pastéis Variados", "preco" => "R$ 38,00"]
];

// Identifica a página para destacar a navegação e montar o cardápio compartilhado.
$page = 'pastel';
$title = 'Kriko’s — pastel';
$individuais = $pasteis_individuais;
require __DIR__ . '/includes/cardapio.php';
