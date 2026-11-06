<?php

namespace Kriko\Services\Validar;

use Exception;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarPagamento extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        $pedidoId = filter_var($dto->obter('pedido_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $valor = filter_var($dto->pedido->getValorTotal(), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $metodo = $dto->obter('metodo');

        if ($pedidoId === false) {
            throw new Exception('Pedido inválido.');
        }

        if ($valor === false) {
            throw new Exception('Valor do pagamento inválido.');
        }

        if (!in_array($metodo, ['PIX', 'CREDITO', 'DEBITO', 'DINHEIRO'], true)) {
            throw new Exception('Método de pagamento inválido.');
        }
    }
}