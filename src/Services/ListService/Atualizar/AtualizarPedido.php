<?php

namespace Kriko\Services\ListService\Atualizar;

use DomainException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

final class AtualizarPedido extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        $pedido = $dto->pedido;
        if (!$pedido) {
            throw new DomainException('Pedido não encontrado.');
        }

        $status = $dto->obter('status', $pedido->getStatus());
        $pagamento = $dto->obter('statusPagamento', $pedido->getStatusPagamento() ?? 'PENDENTE');
        $forma = $dto->obter('forma_pagamento', $pedido->getFormaPagamento());

        if (
            !in_array(
                $status,
                ['ABERTO', 'EM_PREPARACAO', 'PRONTO', 'FINALIZADO', 'CANCELADO'],
                true,
            )
        ) {
            throw new DomainException('Status do pedido inválido.');
        }
        if (!in_array($pagamento, ['PENDENTE', 'PAGO', 'ESTORNADO'], true)) {
            throw new DomainException('Status do pagamento inválido.');
        }
        if (
            $dto->possui('forma_pagamento') &&
            !in_array($forma, ['PIX', 'DINHEIRO', 'CARTAO_CREDITO', 'CARTAO_DEBITO'], true)
        ) {
            throw new DomainException('Forma de pagamento inválida.');
        }
        if (
            in_array($pedido->getStatus(), ['FINALIZADO', 'CANCELADO'], true) &&
            ($status !== $pedido->getStatus() ||
                $pagamento !== $pedido->getStatusPagamento() ||
                $forma !== $pedido->getFormaPagamento())
        ) {
            throw new DomainException('Não é permitido alterar um pedido encerrado.');
        }
        if ($pagamento !== $pedido->getStatusPagamento() && $pagamento !== 'PENDENTE') {
            throw new DomainException('Confirme o recebimento pela rotina de pagamentos.');
        }
        if (
            $pedido->getStatusPagamento() === 'PAGO' &&
            ($pagamento !== 'PAGO' ||
                $forma !== $pedido->getFormaPagamento() ||
                $status === 'CANCELADO')
        ) {
            throw new DomainException('O recebimento já foi confirmado para este pedido.');
        }
        if (
            $status === 'FINALIZADO' &&
            ($pagamento !== 'PAGO' ||
                !in_array($pedido->getStatus(), ['PRONTO', 'FINALIZADO'], true))
        ) {
            throw new DomainException('Somente pedidos prontos e pagos podem ser finalizados.');
        }

        $pedido->setStatus($status)->setStatusPagamento($pagamento);
        if ($dto->possui('forma_pagamento')) {
            $pedido->setFormaPagamento($forma);
        }
    }
}
