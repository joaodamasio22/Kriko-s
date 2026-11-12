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
        $metodo = $dto->pedido->getFormaPagamento();

        if ($pedidoId === false) {
            throw new Exception('Pedido inválido.');
        }

        if (!in_array($metodo, ['PIX','DINHEIRO','CARTAO_CREDITO','CARTAO_DEBITO'], true)) {
            throw new Exception('Método de pagamento inválido.');
        }
    }
}
