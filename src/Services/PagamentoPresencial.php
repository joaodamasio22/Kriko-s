<?php

namespace Kriko\Services;

use Doctrine\DBAL\Connection;
use DomainException;

final class PagamentoPresencial
{
    public function __construct(private readonly Connection $conexao)
    {
    }

    private function responsavel(int $id): array
    {
        $admin = $this->conexao->fetchAssociative(
            "SELECT id, nome, perfil FROM administradores WHERE id = ? AND ativo = 'S'",
            [$id],
        );
        if (!$admin || !in_array($admin['perfil'], ['GARCOM', 'MASTER'], true)) {
            throw new DomainException(
                'Seu acesso não pode confirmar pagamentos ou finalizar pedidos.',
            );
        }
        return $admin;
    }

    public function confirmar(
        int $pedidoId,
        int $responsavelId,
        string $metodo,
        int $valorConferido,
    ): void {
        if (!in_array($metodo, ['PIX', 'DINHEIRO', 'CARTAO_CREDITO', 'CARTAO_DEBITO'], true)) {
            throw new DomainException('Selecione a forma de pagamento recebida.');
        }
        $this->conexao->transactional(function () use (
            $pedidoId,
            $responsavelId,
            $metodo,
            $valorConferido,
        ): void {
            $admin = $this->responsavel($responsavelId);
            $pedido = $this->conexao->fetchAssociative(
                'SELECT * FROM pedidos WHERE id = ? FOR UPDATE',
                [$pedidoId],
            );
            if (!$pedido) {
                throw new DomainException('Pedido não encontrado.');
            }
            // Uma segunda confirmação não altera o registro original do recebimento.
            if (
                $pedido['status_pagamento'] === 'PAGO' &&
                in_array($pedido['status'], ['PRONTO', 'FINALIZADO'], true)
            ) {
                return;
            }
            if ($pedido['status'] !== 'PRONTO') {
                throw new DomainException(
                    'O pedido precisa estar pronto para confirmar o recebimento.',
                );
            }
            if ($pedido['status_pagamento'] !== 'PENDENTE') {
                throw new DomainException('O pagamento desse pedido não está pendente.');
            }
            if ((int) $pedido['valor_total'] <= 0) {
                throw new DomainException('Não é possível confirmar um pedido sem valor.');
            }
            if ((int) $pedido['valor_total'] !== $valorConferido) {
                throw new DomainException(
                    'O valor do pedido mudou. Atualize a página e confira o recebimento novamente.',
                );
            }

            $pagamentos = $this->conexao->fetchAllAssociative(
                "SELECT id, valor, status FROM pagamentos WHERE pedido = ? AND status IN ('PENDENTE', 'APROVADO') FOR UPDATE",
                [$pedidoId],
            );
            if (
                count($pagamentos) > 1 ||
                ($pagamentos &&
                    ($pagamentos[0]['status'] !== 'PENDENTE' ||
                        (int) $pagamentos[0]['valor'] !== (int) $pedido['valor_total']))
            ) {
                throw new DomainException(
                    'Os valores dos pagamentos divergem do pedido. Solicite a conferência do MASTER.',
                );
            }
            $registro = [
                'status' => 'APROVADO',
                'metodo' => $metodo,
                'data_pagamento' => date('Y-m-d H:i:s'),
                'referencia' => json_encode(
                    [
                        'tipo' => 'PRESENCIAL',
                        'responsavel_id' => (int) $admin['id'],
                        'responsavel_nome' => $admin['nome'],
                    ],
                    JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
                ),
            ];
            if ($pagamentos) {
                $this->conexao->update('pagamentos', $registro, ['id' => $pagamentos[0]['id']]);
            } else {
                $this->conexao->insert(
                    'pagamentos',
                    $registro + [
                        'pedido' => $pedidoId,
                        'valor' => (int) $pedido['valor_total'],
                        'data_criacao' => date('Y-m-d H:i:s'),
                    ],
                );
            }
            $this->conexao->update(
                'pedidos',
                ['status_pagamento' => 'PAGO', 'forma_pagamento' => $metodo],
                ['id' => $pedidoId],
            );
        });
    }

    public function finalizar(int $pedidoId, int $responsavelId): void
    {
        $this->conexao->transactional(function () use ($pedidoId, $responsavelId): void {
            $this->responsavel($responsavelId);
            $pedido = $this->conexao->fetchAssociative(
                'SELECT status, status_pagamento FROM pedidos WHERE id = ? FOR UPDATE',
                [$pedidoId],
            );
            if (!$pedido) {
                throw new DomainException('Pedido não encontrado.');
            }
            if ($pedido['status_pagamento'] !== 'PAGO') {
                throw new DomainException('Confirme o recebimento antes de finalizar o pedido.');
            }
            if ($pedido['status'] === 'FINALIZADO') {
                return;
            }
            if ($pedido['status'] !== 'PRONTO') {
                throw new DomainException('Somente pedidos prontos podem ser finalizados.');
            }
            $this->conexao->update('pedidos', ['status' => 'FINALIZADO'], ['id' => $pedidoId]);
        });
    }
}
