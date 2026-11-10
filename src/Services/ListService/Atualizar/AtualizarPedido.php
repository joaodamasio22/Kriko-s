<?php

namespace Kriko\Services\ListService\Atualizar;

use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Exception;

final class AtualizarPedido extends Handler
{
    private array $status_permitido = [
            'ABERTO',
            'EM_PREPARACAO',
            'PRONTO',
            'FINALIZADO',
            'CANCELADO'
        ];

    private array $formas_pagamento = [
            'PIX',
            'DINHEIRO',
            'CARTAO_CREDITO',
            'CARTAO_DEBITO'
        ];

    private array $transicoes_status = [
            'PENDENTE',
            'PAGO',
            'ESTORNADO'
        ];

    protected function executar(OperacaoDTO $dto): void
    {
        $pedido = $dto->pedido;

        if ($pedido === null) {
            throw new Exception('Nenhum pedido foi carregado.');
        }

        if ($dto->possui('status')) {
            $novoStatus = $dto->obter('status');

            if (!is_string($novoStatus) || !in_array($novoStatus, $this->status_permitido, true)) {
                throw new Exception('Status do pedido inválido.');
            }

            $pedido->setStatus($novoStatus);

            
        }

        if ($dto->possui('statusPagamento')) {
            $novoStatusPagamento = $dto->obter('statusPagamento');
            if (!is_string($novoStatusPagamento) || !in_array($novoStatusPagamento, $this->transicoes_status, true)) {
                throw new Exception('Status do pagamento inválido.');
            }
            $pedido->setStatusPagamento($novoStatusPagamento);
        }

        if ($dto->possui('forma_pagamento')) {

            $formaPagamento = $dto->obter('forma_pagamento');

            if (!is_string($formaPagamento) || !in_array($formaPagamento, $this->formas_pagamento, true)) {
                throw new Exception('Forma de pagamento inválida.');
            }

            if ($pedido->getStatusPagamento() !== 'PENDENTE') {
                throw new Exception('Não é permitido alterar a forma de pagamento de um pedido já processado.');
            }

            $pedido->setFormaPagamento($formaPagamento);
        }
    }
}
