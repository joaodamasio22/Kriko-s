<?php

namespace Kriko\Services\ListService\Atualizar;

use DateTime;
use DomainException;
use InvalidArgumentException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Kriko\Entity\Pedido;

class PagamentoAprovado extends Handler
{
    protected function executar(OperacaoDTO $dto): void
    {
        $pagamento = $dto->pagamento;

        if ($pagamento === null) {
            throw new DomainException('Nenhum pagamento foi carregado.');
        }

        if ($pagamento->getStatus() !== 'PENDENTE') {
            throw new DomainException('Somente pagamentos pendentes podem ser aprovados.');
        }

        $referencia = $dto->obter('referencia');

        if (!is_string($referencia) || trim($referencia) === '' || mb_strlen($referencia) > 255) {
            throw new InvalidArgumentException('Referência de pagamento inválida.');
        }

        $pedido = $pagamento->getPedido();

        if ($pedido === null) {
            throw new DomainException('Pagamento para pedido não existente.');
        }

        if ($pedido->getStatus() === 'CANCELADO') {
            throw new DomainException('Não é permitido aprovar pagamento de um pedido cancelado.');
        }

        $totalAprovado = $pagamento->getValor();
        foreach ($pedido->getPagamentos() as $item) {
            if ($item->getId() !== $pagamento->getId() && $item->getStatus() === 'APROVADO') {
                $totalAprovado += $item->getValor();
            }
        }

        $pagamento->setStatus('APROVADO');

        $pagamento->setReferencia(
            trim($referencia)
        );

        $pagamento->setDataPagamento(new DateTime());

        $this->atualizarStatusPedido(
            $pedido,
            $totalAprovado
        );
    }

    private function atualizarStatusPedido(
        Pedido $pedido,
        int $totalAprovado
    ): void {
        $totalPedido = $pedido->getValorTotal();

        if ($totalAprovado >= $totalPedido) {
            $pedido->setStatusPagamento('PAGO');
        }
    }
}
