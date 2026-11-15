<?php

namespace Kriko\Services\Validar;

use DomainException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

class ValidarPagamento extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        $pedidoId = filter_var($dto->obter('pedido_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $metodo = $dto->pedido->getFormaPagamento();

        if ($pedidoId === false) {
            throw new DomainException('Pedido inválido.');
        }

        if (!in_array($metodo, ['PIX', 'DINHEIRO', 'CARTAO_CREDITO', 'CARTAO_DEBITO'], true)) {
            throw new DomainException('Método de pagamento inválido.');
        }
        if (
            $dto->obter('status', 'PENDENTE') !== 'PENDENTE' ||
            $dto->pedido->getStatusPagamento() !== 'PENDENTE' ||
            in_array($dto->pedido->getStatus(), ['FINALIZADO', 'CANCELADO'], true) ||
            $dto->pedido->getValorTotal() <= 0
        ) {
            throw new DomainException(
                'Somente pedidos pendentes e com valor podem registrar pagamento.',
            );
        }
    }
}
