<?php

use Kriko\Services\Control\ChainControl;

require_once 'bootstrap.php';

$dadosAtualizado = [
    'pedido_id' => 1,
    'status' => 'EM_PREPARACAO',
    'forma_pagamento' => 'CARTAO_DEBITO',
    'statusPagamento' => 'PENDENTE'

];

if(!filter_var($dadosAtualizado['pedido_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
    throw new Exception('Identificador do pedido inválido.');

    die();
}

(new ChainControl($entityManager))->atualizarPedido($dadosAtualizado);
