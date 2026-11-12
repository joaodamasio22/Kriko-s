<?php

namespace Kriko\Services\ListService\Atualizar;

use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;
use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;

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

        $pagamento->setStatus('APROVADO');

        $pagamento->setReferencia(
            trim($referencia)
        );

        $pagamento->setDataPagamento(new DateTimeImmutable());

        $this->atualizarStatusPedido(
            $pagamento,
            $pedido
        );
    }

    private function atualizarStatusPedido(
        object $pagamento,
        object $pedido
    ): void {

        $totalPedido = $pedido->getValorTotal();

        $totalAprovado = 0;

        foreach ($pedido->getPagamentos() as $item) {

            if ($item->getStatus() === 'APROVADO') {
                $totalAprovado += $item->getValor();
            }
        }

        if ($totalAprovado >= $totalPedido) {
            $pedido->setStatusPagamento('PAGO');
        }
    }
}
