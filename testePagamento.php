<?php

use Kriko\Services\Control\ChainControl;

require_once 'bootstrap.php';

$dadosPagamento = [
    'pedido_id' => 1,
    'status' => 'PENDENTE'
];

if(!filter_var($dadosPagamento['pedido_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
    throw new Exception('Identificador do pedido inválido.');

    die();
}

(new ChainControl($entityManager))->pagamento($dadosPagamento);

//        --->>>>   TUDO ACIMA ESTÁ CORRETO!!!!!!!    <<<<---

