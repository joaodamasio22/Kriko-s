<?php

use Kriko\Services\Control\ChainControl;

header('Content-Type: application/json');

require_once 'bootstrap.php';

$dados = json_decode(file_get_contents('ENDERECO DA API'), true);

// var_dump($dados);   <<<-- REALIZAR 'var_dump()' PARA DEBUGAR O JSON
// Pedido:
//     1. 'mesa_id' = id da mesa,
//     2. 'admin_id' = id do funcionario que realizou o pedido,
//     3: 'status' = ('ABERTO', 'EM_PREPARACAO', 'PRONTO', 'FINALIZADO', 'CANCELADO')

// itemPedido:
//  1. 'pedido_id' = id do pedido
//  2. 'produto_id' = id do produto
//  3. 'quantidade' = quantidade de quantos produto(iguais) será necessário
//  4. 'preco_unitario' = preco de CADA produto selecionado


if (!is_array($dados)) {
    http_response_code(400);
    echo 'JSON inválido';
    exit;
}

$mesaId = $dados['mesa_id'] ?? null;
$itens = $dados['itens'] ?? [];

if (!filter_var($mesaId, FILTER_VALIDATE_INT) || !is_array($itens) || count($itens) === 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados do pedido inválidos']);

    exit;
}

$chain = new ChainControl($entityManager);

$resultadoPedido = $chain->pedido($dados);

foreach ($itens as $item) {

    $dadosItem = [
        'pedido_id' => 'Precisa retornar PEDIDO',
        'produto_id' => $item['produto_id'],
        'quantidade' => $item['quantidade'],
        'preco_unitario' => $item['preco_unitario']
    ];

    $chain->itemPedido($dadosItem);
}

echo 'Pedido cadastrado com sucesso!';
