<?php

namespace Kriko\Services\ListService\Atualizar;

use Kriko\Chain\Handler;
use Kriko\DTO\OperacaoDTO;
use Exception;

final class AtualizarPedido extends Handler
{
    private array $status_permitido = [
            'PENDENTE',
            'EM_PREPARACAO',
            'PRONTO',
            'FINALIZADO',
            'CANCELADO',
        ];

    private array $formas_pagamento = [
            'PIX',
            'DINHEIRO',
            'CARTAO_CREDITO',
            'CARTAO_DEBITO',
        ];

    private array $transicoes_status = [
            'PENDENTE',
            'EM_PREPARACAO',
            'PRONTO',
            'FINALIZADO',
            'CANCELADO',
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

            $statusAtual = $pedido->getStatus();
 
                if (!in_array($novoStatus, $this->transicoes_status, true)) {
                    throw new Exception("Não é permitido alterar o pedido ". "de {$statusAtual} para {$novoStatus}.");
                }

                $pedido->setStatus($novoStatus);
            
        }

        if ($dto->possui('forma_pagamento')) {

            $formaPagamento = $dto->obter('forma_pagamento');

            if (!is_string($formaPagamento) || !in_array($formaPagamento, $this->formas_pagamento, true)) {
                throw new Exception('Forma de pagamento inválida.');
            }

            if ($pedido->getStatusPagamento() !== 'PENDENTE') {
                throw new Exception('Não é permitido alterar a forma de pagamento de um pedido já processado.');
            }

            $pedido->setFormaPagamento(
                $formaPagamento
            );
        }

        if ($dto->possui('observacao')) {

            $observacao = $dto->obter('observacao');

            if ($observacao !== null && !is_string($observacao)) {
                throw new Exception('Observação inválida.');
            }

            if (in_array($pedido->getStatus(), ['FINALIZADO', 'CANCELADO'], true)) {
                throw new Exception('Não é permitido alterar a observação '. 'de um pedido encerrado.');
            }

            $pedido->setObservacao($observacao);
        }
    }
}
